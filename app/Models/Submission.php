<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'student_id',
        'file',
        'version',
        'notes',
        'status',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class)->latest();
    }

    public function latestReview()
    {
        return $this->hasOne(Review::class)->latestOfMany();
    }
}
