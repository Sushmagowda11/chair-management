<?php

namespace App\Models;
<<<<<<< HEAD
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\UserType; 
=======

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Models\UserType; // ✅ IMPORTANT
>>>>>>> 12d698d386402a5adf1bdb0eee155e55a1882bba

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
<<<<<<< HEAD
        'user_type_id', 
        'status',       
=======
        'user_type_id', // ✅ added
        'status',       // ✅ added
>>>>>>> 12d698d386402a5adf1bdb0eee155e55a1882bba
    ];

    protected $hidden = [
        'password',
    ];

    public function userType()
    {
        return $this->belongsTo(UserType::class);
    }
}
