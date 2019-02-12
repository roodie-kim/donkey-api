<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Image extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'post_id', 'comment_id', 'original_name', 'new_name',
        'ext', 'original_width', 'original_height',
        'thumb_width', 'thumb_height', 'uploaded'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'deleted_at',
    ];

    protected $dates = [
        'deleted_at',
    ];
}
