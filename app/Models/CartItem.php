<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'user_id',
        'product_id',
        'sku_id',
        'quantity',
        'price',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'quantity' => 'integer',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function sku()
    {
        return $this->belongsTo(Sku::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Calculate subtotal for this cart item
    public function getSubtotalAttribute()
    {
        return $this->price * $this->quantity;
    }
    
    // Set the price based on the quantity and SKU pricing rules
    public function setPriceFromSku()
    {
        if ($this->sku) {
            $this->price = $this->sku->getPriceForQuantity($this->quantity);
        }
    }
    
    // Update the price when quantity changes
    public function updatePriceForQuantity()
    {
        $this->setPriceFromSku();
        $this->save();
    }
}