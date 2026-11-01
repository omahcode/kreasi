<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Sinkronkan data laporan PKL yang sudah ada dengan kelompok ploting & guru pembimbing yang ditentukan Admin
        $members = DB::table('pkl_group_members')
            ->join('pkl_groups', 'pkl_group_members.pkl_group_id', '=', 'pkl_groups.id')
            ->leftJoin('companies', 'pkl_groups.company_id', '=', 'companies.id')
            ->select(
                'pkl_group_members.student_id',
                'pkl_groups.id as group_id',
                'pkl_groups.mentor_teacher_id',
                'pkl_groups.company_id',
                'companies.name as company_name',
                'companies.address as company_address'
            )
            ->whereNotNull('pkl_groups.mentor_teacher_id')
            ->get();

        foreach ($members as $member) {
            $update = [
                'pkl_group_id' => $member->group_id,
                'mentor_teacher_id' => $member->mentor_teacher_id,
                'company_id' => $member->company_id,
            ];

            if ($member->company_name) {
                $update['company_name'] = $member->company_name;
            }
            if ($member->company_address) {
                $update['company_address'] = $member->company_address;
            }

            DB::table('pkl_reports')
                ->where('student_id', $member->student_id)
                ->update($update);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
