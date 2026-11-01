<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pkl_reports', function (Blueprint $table) {
            $table->string('title_status')->default('submitted')->after('rejection_note'); // submitted, approved, revision_required
        });

        // Migrate existing records:
        // If a report has status 'approved' but chapters are not fully approved (5/5),
        // adjust title_status to 'approved' and report status to 'in_progress'.
        try {
            $reports = DB::table('pkl_reports')->get();
            foreach ($reports as $report) {
                $approvedChapters = DB::table('pkl_chapters')
                    ->where('report_id', $report->id)
                    ->where('status', 'approved')
                    ->whereNotNull('file')
                    ->count();

                if ($report->status === 'approved') {
                    $titleStatus = 'approved';
                    // If not all 5 chapters are complete, it's actually in progress
                    $newStatus = ($approvedChapters >= 5) ? 'approved' : 'in_progress';
                } elseif ($report->status === 'revision_required') {
                    $titleStatus = 'revision_required';
                    $newStatus = 'revision_required';
                } else {
                    $titleStatus = 'submitted';
                    $newStatus = $report->status;
                }

                DB::table('pkl_reports')->where('id', $report->id)->update([
                    'title_status' => $titleStatus,
                    'status' => $newStatus,
                ]);
            }
        } catch (Throwable $e) {
            // Ignore during initial fresh migration
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pkl_reports', function (Blueprint $table) {
            $table->dropColumn('title_status');
        });
    }
};
