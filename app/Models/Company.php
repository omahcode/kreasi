<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sector',
        'address',
        'city',
        'contact_person',
        'phone',
        'email',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function pklGroups()
    {
        return $this->hasMany(PklGroup::class);
    }

    public function pklReports()
    {
        return $this->hasMany(PklReport::class);
    }
}
