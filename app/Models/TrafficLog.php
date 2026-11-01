<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrafficLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'method',
        'path',
        'route_name',
        'status_code',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
