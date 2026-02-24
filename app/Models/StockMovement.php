<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $table = 'grouped_movements';

    public $timestamps = false;

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'int';

    public function getQualifiedKeyName()
    {
        return 'id';
    }

    public function sku()
    {
        return $this->belongsTo(Sku::class, 'sku_id');
    }
}
