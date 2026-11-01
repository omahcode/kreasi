<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\TrafficLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class TrafficMonitoringController extends Controller
{
    /**
     * Display traffic & storage analytics dashboard.
     */
    public function index(Request $request)
    {
        $today = Carbon::today();
        $sevenDaysAgo = Carbon::today()->subDays(6);

        // 1. Traffic Summary Stats
        $totalHits = TrafficLog::count();
        $hitsToday = TrafficLog::whereDate('created_at', $today)->count();
        $hitsThisWeek = TrafficLog::where('created_at', '>=', $sevenDaysAgo)->count();
        $uniqueIpsToday = TrafficLog::whereDate('created_at', $today)->distinct('ip_address')->count('ip_address');
        $uniqueUsersToday = TrafficLog::whereDate('created_at', $today)->whereNotNull('user_id')->distinct('user_id')->count('user_id');

        // 2. Daily Hits Chart Data (Last 14 Days)
        $chartDates = [];
        $chartHits = [];
        for ($i = 13; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $chartDates[] = $date->format('d M');
            $chartHits[] = TrafficLog::whereDate('created_at', $date)->count();
        }

        // 3. User Role Traffic Breakdown
        $roleHits = [
            'siswa' => 0,
            'guru' => 0,
            'admin' => 0,
            'guest' => 0,
        ];

        $trafficByUsers = TrafficLog::with('user:id,role')
            ->where('created_at', '>=', $sevenDaysAgo)
            ->get();

        foreach ($trafficByUsers as $t) {
            if ($t->user) {
                $r = $t->user->role;
                if (isset($roleHits[$r])) {
                    $roleHits[$r]++;
                } else {
                    $roleHits['siswa']++;
                }
            } else {
                $roleHits['guest']++;
            }
        }

        // 4. Device & Browser Breakdown
        $deviceHits = ['Desktop' => 0, 'Mobile' => 0, 'Tablet / Lainnya' => 0];
        $browserHits = ['Chrome' => 0, 'Firefox' => 0, 'Safari' => 0, 'Edge / Lainnya' => 0];

        foreach ($trafficByUsers as $t) {
            $agent = strtolower($t->user_agent ?? '');

            // Device
            if (str_contains($agent, 'mobile') || str_contains($agent, 'android') || str_contains($agent, 'iphone')) {
                $deviceHits['Mobile']++;
            } elseif (str_contains($agent, 'ipad') || str_contains($agent, 'tablet')) {
                $deviceHits['Tablet / Lainnya']++;
            } else {
                $deviceHits['Desktop']++;
            }

            // Browser
            if (str_contains($agent, 'firefox')) {
                $browserHits['Firefox']++;
            } elseif (str_contains($agent, 'edg')) {
                $browserHits['Edge / Lainnya']++;
            } elseif (str_contains($agent, 'chrome') || str_contains($agent, 'crios')) {
                $browserHits['Chrome']++;
            } elseif (str_contains($agent, 'safari')) {
                $browserHits['Safari']++;
            } else {
                $browserHits['Edge / Lainnya']++;
            }
        }

        // 5. Top 8 Visited Pages
        $topPages = TrafficLog::select('path', DB::raw('count(*) as total'))
            ->groupBy('path')
            ->orderByDesc('total')
            ->take(8)
            ->get();

        // 6. Recent Realtime Access Logs (25 items)
        $recentLogs = TrafficLog::with('user:id,name,role,email')
            ->latest()
            ->take(25)
            ->get();

        // 7. Storage Monitoring
        $storagePath = storage_path('app/public');
        $storageStats = $this->calculateStorageStats($storagePath);

        return view('admin.traffic.index', [
            'totalHits' => $totalHits,
            'hitsToday' => $hitsToday,
            'hitsThisWeek' => $hitsThisWeek,
            'uniqueIpsToday' => $uniqueIpsToday,
            'uniqueUsersToday' => $uniqueUsersToday,
            'chartDates' => $chartDates,
            'chartHits' => $chartHits,
            'roleHits' => $roleHits,
            'deviceHits' => $deviceHits,
            'browserHits' => $browserHits,
            'topPages' => $topPages,
            'recentLogs' => $recentLogs,
            'storage' => $storageStats,
        ]);
    }

    /**
     * Clear old traffic logs older than 30 days.
     */
    public function clearOldLogs(Request $request)
    {
        $cutoff = Carbon::now()->subDays(30);
        $deleted = TrafficLog::where('created_at', '<', $cutoff)->delete();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'CLEAR_TRAFFIC_LOGS',
            'details' => "Membersihkan {$deleted} catatan log trafik lebih dari 30 hari.",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.traffic.index')
            ->with('success', "Berhasil membersihkan {$deleted} log akses lama.");
    }

    /**
     * Calculate storage sizes and inspect files.
     */
    protected function calculateStorageStats(string $basePath): array
    {
        if (! File::exists($basePath)) {
            File::makeDirectory($basePath, 0755, true);
        }

        $categories = [
            'pkl_chapters' => ['label' => 'Laporan PKL (Bab PDF/Docx)', 'icon' => 'ph-file-pdf', 'color' => 'danger', 'bytes' => 0, 'count' => 0],
            'project_submissions' => ['label' => 'Submisi Proyek PjBL (ZIP/RAR)', 'icon' => 'ph-archive', 'color' => 'primary', 'bytes' => 0, 'count' => 0],
            'project_attachments' => ['label' => 'Lampiran & Panduan Proyek', 'icon' => 'ph-paperclip', 'color' => 'info', 'bytes' => 0, 'count' => 0],
            'avatars' => ['label' => 'Foto Profil / Avatar Pengguna', 'icon' => 'ph-user-circle', 'color' => 'warning', 'bytes' => 0, 'count' => 0],
            'other' => ['label' => 'Berkas Lainnya', 'icon' => 'ph-folder', 'color' => 'secondary', 'bytes' => 0, 'count' => 0],
        ];

        $totalBytes = 0;
        $totalFiles = 0;
        $allFilesList = [];

        try {
            $files = File::allFiles($basePath);
            foreach ($files as $file) {
                $relPath = str_replace('\\', '/', $file->getRelativePathname());
                $size = $file->getSize();
                $totalBytes += $size;
                $totalFiles++;

                // Determine category
                $catKey = 'other';
                foreach (array_keys($categories) as $k) {
                    if ($k !== 'other' && str_starts_with($relPath, $k)) {
                        $catKey = $k;
                        break;
                    }
                }

                $categories[$catKey]['bytes'] += $size;
                $categories[$catKey]['count']++;

                $allFilesList[] = [
                    'name' => $file->getFilename(),
                    'relative_path' => $relPath,
                    'size' => $size,
                    'size_formatted' => $this->formatBytes($size),
                    'category' => $categories[$catKey]['label'],
                    'color' => $categories[$catKey]['color'],
                    'extension' => strtolower($file->getExtension()),
                    'modified_at' => Carbon::createFromTimestamp($file->getMTime()),
                ];
            }
        } catch (\Throwable $e) {
            // fallback
        }

        // Sort largest 10 files
        usort($allFilesList, fn ($a, $b) => $b['size'] <=> $a['size']);
        $largestFiles = array_slice($allFilesList, 0, 10);

        // Format category sizes
        foreach ($categories as $k => &$cat) {
            $cat['formatted'] = $this->formatBytes($cat['bytes']);
            $cat['percentage'] = $totalBytes > 0 ? round(($cat['bytes'] / $totalBytes) * 100, 1) : 0;
        }

        // Disk Capacity Check (Simulated or Real)
        $quotaBytes = 5 * 1024 * 1024 * 1024; // 5 GB default application storage quota
        $quotaPercentage = min(100, round(($totalBytes / $quotaBytes) * 100, 2));

        return [
            'total_bytes' => $totalBytes,
            'total_formatted' => $this->formatBytes($totalBytes),
            'total_files' => $totalFiles,
            'categories' => $categories,
            'largest_files' => $largestFiles,
            'quota_bytes' => $quotaBytes,
            'quota_formatted' => $this->formatBytes($quotaBytes),
            'quota_percentage' => $quotaPercentage,
        ];
    }

    protected function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision).' '.$units[$pow];
    }
}
