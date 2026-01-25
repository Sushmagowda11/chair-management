<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


    class Dispatch extends Model
{
    protected $fillable = [
        'order_id',
        'driver_id',
        'vehicle_number',
        'dispatch_date',
        'notes'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
    public function dispatch()
{
    return $this->hasOne(Dispatch::class);
}

}


