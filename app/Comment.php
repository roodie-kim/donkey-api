<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Comment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'post_id', 'body',
    ];

    protected $hidden = [
        'create_at', 'updated_at', 'deleted_at',
    ];

    protected $dates = [
        'deleted_at',
    ];

    protected $appends = [
        'is_mine',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id', 'id');
    }

    public function getIsMineAttribute()
    {
        $user = Auth::guard('api')->user();

        if ($user === NULL) {
            $isMine = FALSE;
        } else {
            if ($this->user_id === $user->id) {
                $isMine = TRUE;
            } else {
                $isMine = FALSE;
            }
        }

        return $isMine;
    }
}
