<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class dotenv extends Model
{
    protected $table = 'dotenv';

    protected $fillable= [
        'hashKey'
    ];
}
