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
        Schema::create('pkl_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('title'); // Judul Laporan PKL
            $table->string('company_name'); // Nama Perusahaan / DUDI
            $table->text('company_address')->nullable();
            $table->foreignId('mentor_teacher_id')->nullable()->constrained('users')->nullOnDelete(); // Guru Pembimbing
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('rejection_note')->nullable();
            $table->string('status')->default('draft'); // draft, submitted, revision_required, approved
            $table->timestamps();
        });

        Schema::create('pkl_chapters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('pkl_reports')->cascadeOnDelete();
            $table->unsignedTinyInteger('chapter_number'); // 1, 2, 3, 4, 5
            $table->string('title')->nullable(); // e.g. Bab I: Pendahuluan
            $table->string('file')->nullable();
            $table->text('notes')->nullable();
            $table->text('feedback')->nullable(); // Catatan revisi / bimbingan guru
            $table->string('status')->default('draft'); // draft, submitted, revision_required, approved
            $table->timestamps();

            $table->unique(['report_id', 'chapter_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pkl_chapters');
        Schema::dropIfExists('pkl_reports');
    }
};
