<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nis',
        'class_id',
        'gender',
        'address',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function projectMembers()
    {
        return $this->hasMany(ProjectMember::class);
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_members', 'student_id', 'project_id')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    public function projectLogs()
    {
        return $this->hasMany(ProjectLog::class);
    }

    public function pklReport()
    {
        return $this->hasOne(PklReport::class);
    }

    public function pklGroupMembers()
    {
        return $this->hasMany(PklGroupMember::class);
    }

    public function pklGroups()
    {
        return $this->belongsToMany(PklGroup::class, 'pkl_group_members', 'student_id', 'pkl_group_id')
                    ->withPivot('is_leader')
                    ->withTimestamps();
    }

    public function currentPklGroup()
    {
        return $this->pklGroups()->where('pkl_groups.status', 'active')->latest()->first();
    }
}
