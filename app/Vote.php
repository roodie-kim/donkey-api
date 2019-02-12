<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Vote extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'board_id', 'post_id', 'type',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    protected $dates = [
        'deleted_at',
    ];
}
