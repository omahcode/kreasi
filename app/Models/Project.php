<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'goals',
        'instructions',
        'type',
        'status',
        'deadline',
        'attachment',
        'rubric',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'deadline' => 'datetime',
        ];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members()
    {
        return $this->hasMany(ProjectMember::class);
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'project_members', 'project_id', 'student_id')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function logs()
    {
        return $this->hasMany(ProjectLog::class)->latest();
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class)->latest();
    }

    public function getLeaderAttribute()
    {
        return $this->members()->where('role', 'leader')->with('student.user')->first();
    }
}
