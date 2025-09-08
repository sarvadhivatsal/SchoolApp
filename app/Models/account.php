<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; 

class Account extends Authenticatable
{
    use HasApiTokens, Notifiable; 

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'role',
        'status'
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    public function teacherProfile()
    {
        return $this->hasOne(Teacher::class, 'account_id');
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isTeacher()
    {
        return $this->role === 'teacher';
    }
}
