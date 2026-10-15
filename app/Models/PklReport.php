<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PklReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'pkl_group_id',
        'company_id',
        'title',
        'company_name',
        'company_address',
        'mentor_teacher_id',
        'start_date',
        'end_date',
        'rejection_note',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function pklGroup()
    {
        return $this->belongsTo(PklGroup::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function mentorTeacher()
    {
        return $this->belongsTo(User::class, 'mentor_teacher_id');
    }

    public function chapters()
    {
        return $this->hasMany(PklChapter::class, 'report_id')->orderBy('chapter_number');
    }
}
