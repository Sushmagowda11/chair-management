<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BomItem extends Model
{
    protected $fillable = [
        'bom_id',
        'component_id',
        'quantity'
    ];

    public function bom()
    {
        return $this->belongsTo(Bom::class);
    }

    public function component()
    {
        return $this->belongsTo(Component::class);
    }
}
