<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'client_name',
        'product_id',
        'quantity',
        'order_date',
        'expected_delivery',
        'total_amount',
        'status',
        'notes',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function components()
{
    return $this->hasMany(OrderComponent::class);
}
public function orderComponents()
{
    return $this->hasMany(OrderComponent::class);
}

}

