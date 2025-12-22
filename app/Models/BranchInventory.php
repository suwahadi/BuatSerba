<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchInventory extends Model
{
    use HasFactory;

    protected $table = 'branch_inventory';

    protected $fillable = [
        'branch_id',
        'sku_id',
        'quantity_available',
        'quantity_reserved',
        'minimum_stock_level',
        'reorder_point',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function sku()
    {
        return $this->belongsTo(Sku::class);
    }
}
