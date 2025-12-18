<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'phone',
        'email',
        'province_id',
        'province_name',
        'city_id',
        'city_name',
        'city_type',
        'subdistrict_id',
        'subdistrict_name',
        'postal_code',
        'full_address',
        'is_active',
        'priority',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function inventory()
    {
        return $this->hasMany(BranchInventory::class);
    }
}
