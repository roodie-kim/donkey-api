<?php

namespace App\Http\Traits;

use Carbon\Carbon;

trait PointsTrait
{
    public function giveAuthPoints($user)
    {
        // 하루 한 번씩만 2포인트 지급
        $pointGiven = $user->points()->where('type', 'login')
            ->whereDate('created_at', Carbon::today())->exists();

        if (!$pointGiven) {
            $user->points()
                ->create([
                    'type' => 'login',
                    'amount' => 2,
                ]);
        }
    }

    public function givePostPoints($user)
    {
        // 하루 10번까지 각 포스트당 1포인트 지급
        $todayPointsCount = $user->points()->whereDate('created_at', Carbon::today())
            ->whereDate('created_at', Carbon::today())->count();

        if ($todayPointsCount <= 10) {
            $user->points()
                ->create([
                    'type' => 'post',
                    'amount' => 1,
                ]);
        }
    }

    public function getVotesPoints($post, $type)
    {
        $amount = $type === 'up' ? 1 : -1;
        $user = $post->user;
        $user->points()
            ->create([
                'type' => 'vote',
                'amount' => $amount,
            ]);
    }
}

