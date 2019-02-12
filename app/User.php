<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes;

    protected $fillable = [
        'email', 'password', 'name', 'remember_token',
        'verification_code', 'email_verified_at', 'delete_flag',
    ];

    protected $hidden = [
        'email', 'password', 'created_at', 'verification_code', 'remember_token',
        'email_verified_at', 'updated_at', 'deleted_at', 'delete_flag', 'is_admin',
    ];

    protected $dates = [
        'deleted_at',
    ];

    protected $casts = [
        'is_admin' => 'boolean',
    ];

    public function images()
    {
        return $this->hasMany(Image::class, 'user_id', 'id');
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'user_id', 'id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'user_id', 'id');
    }

    public function votes()
    {
        return $this->hasMany(Vote::class, 'user_id', 'id');
    }

    public function boardRequest()
    {
        return $this->hasMany(Board_Request::class, 'user_id', 'id');
    }
}
