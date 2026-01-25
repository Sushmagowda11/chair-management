<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
        'component_id',
        'movement_type',
        'quantity',
        'reference',
    ];

    public function component()
    {
        return $this->belongsTo(Component::class);
    }
}
