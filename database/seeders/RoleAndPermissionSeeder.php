<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions
        $permissions = [
            // Admin permissions
            'manage_users',
            'manage_teachers',
            'manage_students',
            'manage_classes',
            'import_excel',
            'view_audit_logs',
            'view_reports',
            // Teacher permissions
            'create_projects',
            'edit_projects',
            'delete_projects',
            'assign_groups',
            'review_submissions',
            'grade_projects',
            'mentor_pkl',
            // Student / Group Leader permissions
            'view_projects',
            'submit_tasks',
            'create_logbook',
            'view_feedback',
            'submit_pkl_title',
            'upload_pkl_chapters',
            // Group leader specific
            'manage_group_progress',
            'submit_group_report',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Roles and assign permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions(Permission::all());

        $guruRole = Role::firstOrCreate(['name' => 'guru']);
        $guruRole->syncPermissions([
            'create_projects',
            'edit_projects',
            'delete_projects',
            'assign_groups',
            'review_submissions',
            'grade_projects',
            'mentor_pkl',
            'view_projects',
        ]);

        $ketuaRole = Role::firstOrCreate(['name' => 'ketua_kelompok']);
        $ketuaRole->syncPermissions([
            'view_projects',
            'submit_tasks',
            'create_logbook',
            'view_feedback',
            'submit_pkl_title',
            'upload_pkl_chapters',
            'manage_group_progress',
            'submit_group_report',
        ]);

        $siswaRole = Role::firstOrCreate(['name' => 'siswa']);
        $siswaRole->syncPermissions([
            'view_projects',
            'submit_tasks',
            'create_logbook',
            'view_feedback',
            'submit_pkl_title',
            'upload_pkl_chapters',
        ]);
    }
}
