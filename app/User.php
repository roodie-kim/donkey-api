<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use App\Level_Range;


class User extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes;

    protected $fillable = [
        'email', 'password', 'name', 'remember_token',
        'verification_code', 'email_verified_at', 'delete_flag',
    ];

    protected $hidden = [
        'email', 'password', 'created_at', 'verification_code', 'remember_token',
        'email_verified_at', 'updated_at', 'deleted_at', 'delete_flag', 'is_admin',
    ];

    protected $dates = [
        'deleted_at',
    ];

    protected $appends = ['level_info'];

    protected $casts = [
        'is_admin' => 'boolean',
    ];

    public function images()
    {
        return $this->hasMany(Image::class, 'user_id', 'id');
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'user_id', 'id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'user_id', 'id');
    }

    public function votes()
    {
        return $this->hasMany(Vote::class, 'user_id', 'id');
    }

    public function points()
    {
        return $this->hasMany(Point::class, 'user_id', 'id');
    }

    public function boardRequest()
    {
        return $this->hasMany(Board_Request::class, 'user_id', 'id');
    }

    public function getLevelInfoAttribute()
    {
        $levelInfo = new \stdClass();
        $points = (int) $this->points()->sum('amount');

        $levelInfo->points = $points;
        $levelRange = $this->getLevelRange();
        foreach ($levelRange as $range) {
            if ($range->min_points <= $points && $points <= $range->max_points) {
                $levelInfo->level = $range->id;
                return $levelInfo;
            }
        }
    }

    public function getLevelRange()
    {
        if (Cache::has('level_range')) {
            $levelRange = Cache::get('level_range');
        } else {
            $levelRange = Level_Range::get();
            Cache::forever('level_range', $levelRange);
        }
        return $levelRange;
    }

}
