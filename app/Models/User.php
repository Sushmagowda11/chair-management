<?php

namespace App\Models;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\UserType; 

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type_id', 
        'status',       
    ];

    protected $hidden = [
        'password',
    ];

    public function userType()
    {
        return $this->belongsTo(UserType::class);
    }
}
