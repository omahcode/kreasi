<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PklGroupMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'pkl_group_id',
        'student_id',
        'is_leader',
    ];

    protected function casts(): array
    {
        return [
            'is_leader' => 'boolean',
        ];
    }

    public function pklGroup()
    {
        return $this->belongsTo(PklGroup::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
