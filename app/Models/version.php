<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class version extends Model
{
    protected $table = 'version';

protected $fillable = [
            'version',
            'version_panel',

        ];
}
