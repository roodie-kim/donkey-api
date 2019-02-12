<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\User;

class LoginController extends Controller
{
    protected function user(Request $request)
    {
        $user = $request->user();
        return response()->json($user);
    }

    protected function adminUser(Request $request)
    {
        $user = $request->user();
        return response()->json($user);
    }

    protected function login(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
        ]);

        if ($validation->fails()) {
            return response()->json($validation->errors(), 400);
        }

        $user = User::where('email', '=', $request->email)->first();

        if ($user === null) {
            return response()->json(['message' => '잘못된 이메일 주소입니다.'], 401);
        }

        if (Hash::check($request->password, $user->password)) {
            $token = $user->createToken('project')->accessToken;
            return response()->json(['access_token' => $token]);
        } else {
            return response()->json(['message' => '잘못된 비밀번호입니다.'], 401);
        }
    }

    protected function adminLogin(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
        ]);

        if ($validation->fails()) {
            return response()->json($validation->errors(), 400);
        }

        $user = User::where('email', '=', $request->email)->first();

        if (!$user->is_admin) {
            return response()->json(['message' => 'not an admin user'], 401);
        }

        if ($user === null) {
            return response()->json(['email' => 'email not found'], 401);
        }

        if (Hash::check($request->password, $user->password)) {
            $token = $user->createToken('project')->accessToken;
            return response()->json(['access_token' => $token]);
        } else {
            return response()->json(['password' => 'wrong password'], 401);
        }
    }
}
