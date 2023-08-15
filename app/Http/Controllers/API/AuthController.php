<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    //
    public function register(Request $request)
    {
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password); // bcrypt or hash both same
        $user->save();
        return $user;
    }

    public function login(Request $request)
    {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        if (Auth::guard('web')->attempt($credentials)) {
            $user = Auth::guard('web')->user();
            // $user->api_token = Str::random(60);
            $api_token = Str::random(60);
            User::where("id",$user->id)->update(['api_token' => $api_token]);
            $data = User::find($user->id);
            return response()->json($data, 200);
        }

        return response()->json(['message' => 'Something went wrong'], 401);
    }

    public function logout(Request $request)
    {
        $user = Auth::guard('api')->user();
        // $user->api_token = null;
        // $user->save();
        $api_token = null;
        User::where("id",$user->id)->update(['api_token' => $api_token]);
        return response()->json(['message' => 'You are successfully logged out'], 200);
    }

    public function getUser(Request $request)
    {
        if (Auth::guard('api')->check())
        {
            $all = Auth::guard('api')->user();
            return response()->json(['data' => $all], 200);
        }else{
            return response()->json(['message' => 'Something went wrong'], 401);
        }
    }
}
