<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Board_Category extends Model
{
    protected $table = 'board_categories';
    protected $fillable = ['name'];

    public $timestamps = false;

    public function topIndex()
    {
        return $this->belongsTo(Top_Index::class, 'top_index_id', 'id');
    }

    public function boards()
    {
        return $this->hasMany(Board::class, 'board_category_id', 'id');
    }
}
