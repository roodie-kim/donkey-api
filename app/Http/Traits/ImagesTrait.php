<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Image;
use DB;

trait ImagesTrait
{
    public function saveImage($user, $requestImage)
    {
        $originalFileName = pathinfo($requestImage->getClientOriginalName(),PATHINFO_FILENAME);
        $originalFileExtension = $requestImage->getClientOriginalExtension();

        $url = '/' . env('APP_ENV') . '/' . $user->id . '/' ;
        $newImageName = $this->imageName();

        $image = Image::make($requestImage);

        if ($originalFileExtension === 'gif') {
            $originalImage = $this->resize($image, 'original');
            $originalDimension = $this->getDimension($originalImage);
            $originalUpload = Storage::putFileAs($url, $requestImage, $newImageName . '.' .$originalFileExtension);
            Storage::setVisibility($url . '/' . $newImageName . '.' . $originalFileExtension, 'public');
        } else {
            // 1024 기준으로 변경한 이미지 저장 (original)
            $originalImage = $this->resize($image, 'original');
            $originalDimension = $this->getDimension($originalImage);
            $originalPath = $url . $newImageName . '.' . $originalFileExtension;
            $originalUpload = Storage::put($originalPath, (string) $originalImage->stream(), 'public');
        }

        // 512 기준으로 변경한 이미지 저장 (thumbnail)
        $thumbImage = $this->resize($image, 'thumb');
        $thumbDimension = $this->getDimension($thumbImage);
        $thumbPath = $url . $newImageName . '_thumb.' . $originalFileExtension;
        $thumbUpload = Storage::put($thumbPath, (string) $thumbImage->stream(), 'public');

        $uploaded = $originalUpload && $thumbUpload;

        // db 저장할 array 생성
        $dataArray = [
            'original_name' => $originalFileName,
            'new_name' => $newImageName,
            'ext' => $originalFileExtension,
            'original_width' => $originalDimension->width,
            'original_height' => $originalDimension->height,
            'thumb_width' => $thumbDimension->width,
            'thumb_height' => $thumbDimension->height,
            'uploaded' => $uploaded,
        ];

        DB::beginTransaction();
        try {
            $image = $user->images()->create($dataArray);
        } catch (Exception $e) {
            DB::rollBack();
            return FALSE;
        }
        DB::commit();

        return $image;
    }

    public function imageName()
    {
        $randomStr = Carbon::now()->timestamp . '_' . str_random(20);
        return $randomStr;
    }

    private function resize($image, $size)
    {
        if ($size === 'original') {
            $maxSize = 1024;
        } else {
            $maxSize = 512;
        }

        if ($image->height() < $maxSize && $image->width() < $maxSize) {
            return $image;
        }

        if ($size === 'original') {
            // wide image
            if ($image->height() <= $image->width()) {
                return $image->widen($maxSize);
            }
            // tall image
            if ($image->height() > $image->width()) {
                return $image->heighten($maxSize);
            }
        } else {
            return $image->widen($maxSize);
        }
    }

    private function getDimension($image)
    {
        $dimension = new \stdClass();
        $dimension->width = $image->width();
        $dimension->height = $image->height();
        return $dimension;
    }
}