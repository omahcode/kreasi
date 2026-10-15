<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PklGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'company_id',
        'mentor_teacher_id',
        'academic_year_id',
        'start_date',
        'end_date',
        'notes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function mentorTeacher()
    {
        return $this->belongsTo(User::class, 'mentor_teacher_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function members()
    {
        return $this->hasMany(PklGroupMember::class);
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'pkl_group_members', 'pkl_group_id', 'student_id')
                    ->withPivot('is_leader')
                    ->withTimestamps();
    }

    public function leader()
    {
        return $this->hasOne(PklGroupMember::class)->where('is_leader', true);
    }

    public function pklReports()
    {
        return $this->hasMany(PklReport::class);
    }
}
