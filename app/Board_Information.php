<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Board_Information extends Model
{
    protected $table = 'board_informations';

    protected $fillable = ['board_id', 'description', 'seo_image'];
}
