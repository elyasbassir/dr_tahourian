<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use mysql_xdevapi\Exception;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;

class controller_auth extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|unique:users|max:255',
            'password' => 'required|max:255',
        ]);
        if ($validator->fails()) {
            return $validator->messages();
        }
        $user = new User([
            'phone' => $request->phone,
            'password' => bcrypt($request->password),
        ]);
        $user->save();
        $token = JWTAuth::fromUser($user);
        return response()->json(['token' => $token], 201);
    }

    public function login(Request $request)
    {

        $token = JWTAuth::getToken();
        JWTAuth::toUser($token);

        $credentials = $request->only('phone', 'password');
        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return response()->json(['token' => $token]);
    }


}
