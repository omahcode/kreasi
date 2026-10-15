<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class KreasiInitialSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Academic Year
        $academicYear = AcademicYear::firstOrCreate(
            ['name' => '2026/2027'],
            ['is_active' => true]
        );

        // 2. Classes
        $class12Rpl1 = SchoolClass::firstOrCreate(
            ['name' => 'XII RPL 1'],
            ['grade_level' => 'XII', 'academic_year_id' => $academicYear->id]
        );

        $class12Rpl2 = SchoolClass::firstOrCreate(
            ['name' => 'XII RPL 2'],
            ['grade_level' => 'XII', 'academic_year_id' => $academicYear->id]
        );

        $class11Rpl1 = SchoolClass::firstOrCreate(
            ['name' => 'XI RPL 1'],
            ['grade_level' => 'XI', 'academic_year_id' => $academicYear->id]
        );

        $class10Rpl1 = SchoolClass::firstOrCreate(
            ['name' => 'X RPL 1'],
            ['grade_level' => 'X', 'academic_year_id' => $academicYear->id]
        );

        // 3. Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@kreasi.test'],
            [
                'name' => 'Administrator KREASI RPL',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => 'active',
                'phone' => '081234567890',
            ]
        );
        $admin->syncRoles('admin');

        // 4. Guru User & Profile
        $guruUser = User::firstOrCreate(
            ['email' => 'guru@kreasi.test'],
            [
                'name' => 'Budi Santoso, S.Kom., M.T.',
                'password' => Hash::make('password'),
                'role' => 'guru',
                'status' => 'active',
                'phone' => '081234567891',
            ]
        );
        $guruUser->syncRoles('guru');

        Teacher::firstOrCreate(
            ['user_id' => $guruUser->id],
            [
                'teacher_number' => '198507122010011002',
                'specialization' => 'Pemrograman Web & Mobile (PjBL)',
            ]
        );

        // 5. Ketua Kelompok User & Profile
        $ketuaUser = User::firstOrCreate(
            ['email' => 'ketua@kreasi.test'],
            [
                'name' => 'Ahmad Fauzan',
                'password' => Hash::make('password'),
                'role' => 'ketua_kelompok',
                'status' => 'active',
                'phone' => '081234567892',
            ]
        );
        $ketuaUser->syncRoles('ketua_kelompok');

        Student::firstOrCreate(
            ['user_id' => $ketuaUser->id],
            [
                'nis' => '2026120101',
                'class_id' => $class12Rpl1->id,
                'gender' => 'L',
                'address' => 'Jl. Pendidikan RPL No. 10',
            ]
        );

        // 6. Siswa User & Profile
        $siswaUser = User::firstOrCreate(
            ['email' => 'siswa@kreasi.test'],
            [
                'name' => 'Siti Rahmawati',
                'password' => Hash::make('password'),
                'role' => 'siswa',
                'status' => 'active',
                'phone' => '081234567893',
            ]
        );
        $siswaUser->syncRoles('siswa');

        $student2 = Student::firstOrCreate(
            ['user_id' => $siswaUser->id],
            [
                'nis' => '2026120102',
                'class_id' => $class12Rpl1->id,
                'gender' => 'P',
                'address' => 'Jl. Teknologi Kreatif No. 45',
            ]
        );

        // 7. Mitra Industri DU/DI (dimasukkan oleh Admin)
        $company1 = \App\Models\Company::firstOrCreate(
            ['name' => 'PT. Solusi Teknologi Nusantara'],
            [
                'sector' => 'Software House & Cloud Services',
                'address' => 'Gedung Cyber 2 Lantai 15, Jl. HR Rasuna Said Blok X-5',
                'city' => 'Jakarta Selatan',
                'contact_person' => 'Bambang Irawan, S.Kom (HRD)',
                'phone' => '081299887766',
                'email' => 'internship@solusitek.co.id',
                'is_active' => true,
            ]
        );

        $company2 = \App\Models\Company::firstOrCreate(
            ['name' => 'PT. Telkom Akses Digital'],
            [
                'sector' => 'Infrastruktur Jaringan & Telekomunikasi',
                'address' => 'Jl. Gegerkalong Hilir No. 47',
                'city' => 'Bandung',
                'contact_person' => 'Dewi Sartika (Talent Acquisition)',
                'phone' => '081388776655',
                'email' => 'karir@telkomakses.co.id',
                'is_active' => true,
            ]
        );

        $company3 = \App\Models\Company::firstOrCreate(
            ['name' => 'CV. Kreasi Inovasi Media'],
            [
                'sector' => 'Digital Agency & Web Development',
                'address' => 'Jl. Pemuda No. 88',
                'city' => 'Surabaya',
                'contact_person' => 'Rendra Pratama',
                'phone' => '085733221100',
                'email' => 'hello@kreasimedia.id',
                'is_active' => true,
            ]
        );

        // 8. Ploting Kelompok PKL Siswa
        $student1 = Student::where('user_id', $ketuaUser->id)->first();
        if ($student1 && $company1) {
            $pklGroup = \App\Models\PklGroup::firstOrCreate(
                ['name' => 'Kelompok PKL Web Dev - PT Solusi Teknologi Nusantara'],
                [
                    'company_id' => $company1->id,
                    'mentor_teacher_id' => $guruUser->id,
                    'academic_year_id' => $academicYear->id,
                    'start_date' => now()->toDateString(),
                    'end_date' => now()->addMonths(6)->toDateString(),
                    'notes' => 'Divisi Pengembangan Web & Mobile Application. Jam kerja: Senin - Jumat 08.00 - 17.00 WIB.',
                    'status' => 'active',
                ]
            );

            // Assign members: Ketua (leader) & Siswa
            \App\Models\PklGroupMember::firstOrCreate(
                [
                    'pkl_group_id' => $pklGroup->id,
                    'student_id' => $student1->id,
                ],
                ['is_leader' => true]
            );

        }
    }
}
