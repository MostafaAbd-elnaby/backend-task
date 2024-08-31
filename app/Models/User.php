<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'phone',
        'password',
        'verification_code',
        'is_verified',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'verification_code',
    ];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    protected static function booted()
    {
        static::creating(function ($user) {
            $user->verification_code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        });
    }
}
