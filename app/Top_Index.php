<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Top_Index extends Model
{
    protected $table = 'top_index';

    protected $fillable = ['name'];

    public $timestamps = false;

    public function boardCategories()
    {
        return $this->hasMany(Board_Category::class, 'top_index_id', 'id');
    }
}
