<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PklChapter extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'chapter_number',
        'title',
        'file',
        'notes',
        'feedback',
        'status',
    ];

    public function report()
    {
        return $this->belongsTo(PklReport::class, 'report_id');
    }
}
