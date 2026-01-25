<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Component extends Model
{
    use HasFactory;

    protected $fillable = [
        'component_code',
        'component_name',
        'category',
        'unit',
        'current_stock',
        'minimum_stock',
        'price',
        'vendor',
        'specifications',
        'status',
    ];
}
