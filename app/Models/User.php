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
        'user_type_id',
        'status',
        'name',
        'email',
        'password',
        'password_plain',
        'created_by',
        'updated_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function userType()
    {
        return $this->belongsTo(UserType::class);
    }
}
