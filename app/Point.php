<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Point extends Model
{
    protected $fillable = [
        'user_id', 'type', 'amount',
    ];

    protected $hidden = [
        'created_at', 'updated_at',
    ];
}
