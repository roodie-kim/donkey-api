<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Password_Reset;
use App\User;
use Mail;

class ResetPasswordController extends Controller
{
    public function sendEmail (Request $request)
    {
        $validation = Validator::make($request->all(), [
            'email' => 'required|string',
        ]);

        if ($validation->fails()) {
            return response()->json($validation->errors(), 400);
        }

        $model = [
            'email' => $request->email,
            'token' => str_random(60),
        ];

        $model = Password_Reset::create($model);
        $to = new \stdClass();
        $to->email = $request->email;
        $to->name = null;

        $result = Mail::to($to)
            ->send(new \App\Mail\PasswordReset($model['token'], $request['email']));

        return response()->json($result);
    }

    public function resetPassword (Request $request)
    {
        $validation = Validator::make($request->all(), [
            'email' => 'required|string',
            'token' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validation->fails()) {
            return response()->json($validation->errors(), 400);
        }

        $resetLogExists = Password_Reset::where('token', $request->token)
            ->where('email', $request->email)->exists();

        if (!$resetLogExists) {
            return response()->json(['message' => 'wrong request'], 400);
        }

        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();
        $resetLogs = Password_Reset::where('email', $request->email)->delete();

        return response()->json($request);
    }
}
