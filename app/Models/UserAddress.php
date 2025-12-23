<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'province_id',
        'province_name',
        'city_id',
        'city_name',
        'city_type',
        'district_id',
        'district_name',
        'subdistrict_id',
        'subdistrict_name',
        'postal_code',
        'full_address',
        'is_primary',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }
}
