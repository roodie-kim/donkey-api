<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Post extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'board_id', 'title', 'body', 'view_count', 'hided_at',
    ];

    protected $hidden = [
        'deleted_at',
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

    public function comments()
    {
        return $this->hasMany(Comment::class, 'post_id', 'id');
    }

    public function votes()
    {
        return $this->hasMany(Vote::class, 'post_id', 'id');
    }

    public function myVote()
    {
        $user = Auth::guard('api')->user();

        if ($user === null) {
            $userId = null;
        } else {
            $userId = $user->id;
        }

        return $this->hasOne(Vote::class, 'post_id', 'id')->where('user_id', $userId);
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
