<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class GivePoints implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $user;
    protected $pointType;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user, $pointType)
    {
        $this->user = $user;
        $this->pointType = $pointType;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->pointType === 'login') {
            $amount = 5;
        } else if ($this->pointType === 'post') {
            $amount = 1;
        } else if ($this->pointType === 'voteUp') {
            $amount = 1;
        } else if ($this->pointType === 'voteDown') {
            $amount = -1;
        } else {
            return;
        }

        $this->user->point()
            ->create([
                'type' => $this->pointType,
                'amount' => $amount,
            ]);
    }
}
