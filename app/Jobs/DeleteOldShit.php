<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Carbon\Carbon;
use DB;
use App\Image;

class DeleteOldShit implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $baseTime = Carbon::now()->subHours(2)->toDateTimeString();

        DB::table('posts')->where('created_at', '<=', $baseTime)->delete();
        DB::table('comments')->where('created_at', '<=', $baseTime)->delete();

        // DeleteImage Job for each image
        $images = Image::where('created_at', '<=', $baseTime)->get();
        foreach ($images as $image) {
            DeleteImage::dispatch($image);
        }
    }
}
