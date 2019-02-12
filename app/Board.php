<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Board extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'board_category_id',
    ];

    protected $hidden = [
        'create_at', 'updated_at', 'deleted_at',
    ];

    protected $dates = [
        'deleted_at',
    ];

    public function getRouteKeyName()
    {
        return 'name';
    }

    public function boardInformation()
    {
        return $this->hasOne(Board_Information::class, 'board_id', 'id');
    }

    public function boardCategory()
    {
        return $this->belongsTo(Board_Category::class, 'board_category_id', 'id');
    }
}
