<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Password_Reset extends Model
{
    protected $table = 'password_resets';

    protected $fillable = ['email', 'token', 'created_at'];
    public $timestamps = false;
}
