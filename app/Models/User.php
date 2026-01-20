<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Models\UserType; // ✅ IMPORTANT

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type_id', // ✅ added
        'status',       // ✅ added
    ];

    protected $hidden = [
        'password',
    ];

    public function userType()
    {
        return $this->belongsTo(UserType::class);
    }
}
