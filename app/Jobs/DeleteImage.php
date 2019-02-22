<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Illuminate\Support\Facades\Storage;

class DeleteImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $image;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($image)
    {
        $this->image = $image;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $imageUrls = $this->makeUrls($this->image);

        Storage::disk('s3')->delete($imageUrls->original);
        Storage::disk('s3')->delete($imageUrls->thumb);

        $this->image->forceDelete();
    }

    public function makeUrls($image)
    {
        $images = new \stdClass();
        $images->original = '/' . env('APP_ENV') . '/' . $image->user_id . '/' . $image->new_name . '.' . $image->ext;
        $images->thumb = '/' . env('APP_ENV') . '/' . $image->user_id . '/' . $image->new_name . '_thumb.' . $image->ext;

        return $images;
    }
}
