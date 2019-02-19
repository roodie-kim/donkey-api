<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\User;

use DB;

class RegisterController extends Controller
{
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'name' => 'required|unique:users|min:3|max:20',
        ]);

        if ($validation->fails()) {
            return response()->json($validation->errors(), 400);
        }

        $tempCodeExists = TRUE;
        while ($tempCodeExists) {
            $verificationCode = str_random(10);
            $tempCodeExists = User::where('verification_code', $verificationCode)->exists();
        }
        DB::beginTransaction();
        try {
            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'name' => $request->name,
                'verification_code' => $verificationCode,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'internal server error'], 500);
        }
        DB::commit();

        $token = $user->createToken('tv')->accessToken;
        return response()->json(['access_token' => $token], 201);
    }
}
