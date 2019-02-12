<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use App\Http\Traits\ImagesTrait;

class ImagesController extends Controller
{
    use ImagesTrait;

    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
        ]);

        if ($validation->fails()) {
            return response()->json($validation->errors(), 400);
        }

        $user = $request->user();
        $image = $this->saveImage($user, $request->image);

        return response()->json($image, 200);
    }
}
