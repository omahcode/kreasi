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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('goals')->nullable(); // Tujuan
            $table->text('instructions')->nullable(); // Instruksi
            $table->string('type')->default('group'); // group, individual
            $table->string('status')->default('active'); // draft, active, completed, archived
            $table->dateTime('deadline')->nullable();
            $table->string('attachment')->nullable(); // File lampiran dari guru
            $table->text('rubric')->nullable(); // Rubrik penilaian
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('project_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('role')->default('member'); // leader, member
            $table->timestamps();

            $table->unique(['project_id', 'student_id']);
        });

        Schema::create('project_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->text('description');
            $table->string('image')->nullable();
            $table->integer('progress_percentage')->default(0);
            $table->timestamps();
        });

        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('file');
            $table->integer('version')->default(1);
            $table->text('notes')->nullable();
            $table->string('status')->default('submitted'); // submitted, reviewed, revision_required, approved
            $table->timestamps();
        });

        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('submissions')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->text('comment');
            $table->decimal('score', 5, 2)->nullable();
            $table->string('status')->default('reviewed'); // reviewed, revision_required, approved
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('submissions');
        Schema::dropIfExists('project_logs');
        Schema::dropIfExists('project_members');
        Schema::dropIfExists('projects');
    }
};
