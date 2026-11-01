<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user()->load(['teacher', 'student.schoolClass']);

        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:12288'], // Up to 12MB allowed; auto-compressed
            'current_password' => ['nullable', 'required_with:new_password'],
            'new_password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        if ($request->filled('new_password')) {
            if (! Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Kata sandi lama tidak sesuai.']);
            }
            $user->password = Hash::make($request->new_password);
        }

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = 'avatar_'.$user->id.'_'.time().'.webp';
            $relativeDir = 'avatars';
            $targetDir = storage_path('app/public/'.$relativeDir);

            if (! file_exists($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            $destination = $targetDir.'/'.$filename;
            $compressed = $this->compressAndStoreAvatar($file->getRealPath(), $destination);

            if ($compressed) {
                // Delete previous custom avatar if exists
                if ($user->avatar && ! str_starts_with($user->avatar, 'http') && file_exists(storage_path('app/public/'.$user->avatar))) {
                    @unlink(storage_path('app/public/'.$user->avatar));
                }
                $user->avatar = $relativeDir.'/'.$filename;
            } else {
                $avatarPath = $file->store('avatars', 'public');
                $user->avatar = $avatarPath;
            }
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->save();

        AuditLog::record($user->id, 'update_profile', 'Memperbarui profil akun');

        return back()->with('success', 'Profil Anda berhasil diperbarui.');
    }

    /**
     * Dedicated method for updating account password.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'different:current_password'],
        ], [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi baru minimal harus terdiri dari 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
            'password.different' => 'Kata sandi baru harus berbeda dengan kata sandi saat ini.',
        ]);

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Kata sandi saat ini tidak sesuai dengan data kami.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        AuditLog::record($user->id, 'change_password', 'Pengguna berhasil memperbarui kata sandi akun');

        return back()->with('success', 'Kata sandi akun Anda berhasil diperbarui.');
    }

    /**
     * Compress and optimize avatar image using PHP GD to WebP format.
     */
    protected function compressAndStoreAvatar(string $sourcePath, string $destinationPath, int $quality = 82, int $maxDimension = 1000): bool
    {
        if (! extension_loaded('gd')) {
            return false;
        }

        $imageInfo = @getimagesize($sourcePath);
        if (! $imageInfo) {
            return false;
        }

        $mime = $imageInfo['mime'] ?? '';
        $srcImage = match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($sourcePath),
            'image/png' => @imagecreatefrompng($sourcePath),
            'image/webp' => @imagecreatefromwebp($sourcePath),
            default => null,
        };

        if (! $srcImage) {
            return false;
        }

        $origWidth = imagesx($srcImage);
        $origHeight = imagesy($srcImage);

        // Calculate target dimensions keeping aspect ratio
        if ($origWidth > $maxDimension || $origHeight > $maxDimension) {
            if ($origWidth >= $origHeight) {
                $targetWidth = $maxDimension;
                $targetHeight = (int) round(($origHeight / $origWidth) * $maxDimension);
            } else {
                $targetHeight = $maxDimension;
                $targetWidth = (int) round(($origWidth / $origHeight) * $maxDimension);
            }
        } else {
            $targetWidth = $origWidth;
            $targetHeight = $origHeight;
        }

        $dstImage = imagecreatetruecolor($targetWidth, $targetHeight);

        // Preserve transparency for PNG/WebP
        imagealphablending($dstImage, false);
        imagesavealpha($dstImage, true);
        $transparent = imagecolorallocatealpha($dstImage, 255, 255, 255, 127);
        imagefilledrectangle($dstImage, 0, 0, $targetWidth, $targetHeight, $transparent);

        imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $targetWidth, $targetHeight, $origWidth, $origHeight);

        $saved = imagewebp($dstImage, $destinationPath, $quality);

        imagedestroy($srcImage);
        imagedestroy($dstImage);

        return $saved && file_exists($destinationPath);
    }
}
