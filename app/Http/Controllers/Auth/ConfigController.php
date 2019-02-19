<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class ConfigController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        return response()->json($user);
    }

    public function changeNickname(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required|unique:users|min:3|max:20',
        ]);

        if ($validation->fails()) {
            return response()->json($validation->errors(), 400);
        }

        $user = $request->user();
        $user->name = $request->name;
        $user->save();

        return response()->json($user);
    }

    public function changePassword(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'current' => 'required|string',
            'new' => 'required|string|min:6',
        ]);

        if ($validation->fails()) {
            return response()->json($validation->errors(), 400);
        }

        $user = $request->user();

        if (Hash::check($request->current, $user->password)) {
            $user->password = Hash::make($request->new);
            $user->save();
        } else {
            return response()->json(['message' => '기존 비밀번호가 잘못되었습니다.'], 401);
        }

        return response()->json(['message' => 'password changed'], 200);
    }
}
