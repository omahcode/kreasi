<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tabel Perusahaan / DU/DI (dimasukkan oleh Admin)
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama Perusahaan / Industri
            $table->string('sector')->nullable(); // Bidang Usaha (e.g. Software Development, IT Solution)
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('contact_person')->nullable(); // PIC Industri
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Tabel Kelompok PKL (Ploting oleh Admin & Guru)
        Schema::create('pkl_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. "Kelompok PKL PT Solusi Digital"
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('mentor_teacher_id')->constrained('users')->cascadeOnDelete(); // Guru Pembimbing
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->nullOnDelete();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('active'); // active, completed
            $table->timestamps();
        });

        // 3. Anggota Kelompok PKL Siswa
        Schema::create('pkl_group_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pkl_group_id')->constrained('pkl_groups')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->boolean('is_leader')->default(false);
            $table->timestamps();

            $table->unique(['pkl_group_id', 'student_id']);
        });

        // 4. Tambah relasi pkl_group_id dan company_id ke pkl_reports
        Schema::table('pkl_reports', function (Blueprint $table) {
            $table->foreignId('pkl_group_id')->nullable()->after('student_id')->constrained('pkl_groups')->nullOnDelete();
            $table->foreignId('company_id')->nullable()->after('pkl_group_id')->constrained('companies')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pkl_reports', function (Blueprint $table) {
            $table->dropForeign(['pkl_group_id']);
            $table->dropColumn('pkl_group_id');
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });

        Schema::dropIfExists('pkl_group_members');
        Schema::dropIfExists('pkl_groups');
        Schema::dropIfExists('companies');
    }
};
