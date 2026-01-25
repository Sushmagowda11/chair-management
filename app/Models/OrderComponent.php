<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderComponent extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'component_id',
        'component_name',
        'component_unit',
        'quantity_per_unit',
        'total_quantity',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function component()
{
    return $this->belongsTo(Component::class);
}

}
