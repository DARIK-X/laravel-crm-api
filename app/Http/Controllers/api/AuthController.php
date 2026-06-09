<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\MainHelper;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    public function register(Request $request){
        try{
            $data = $request->validate([
                'email' => 'required|string|email|max:255|unique:users',
                'phone' => 'required|string|min:3|max:20|unique:users',
                'password' => 'required|string',
            ]);
        }catch (\Exception $exception){
            return response()->json([
                "error"=> "The request body is not valid",
            ], 422);
        }

        $user = User::query()->create([
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'access_token' => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'login' => 'required',
                'password' => 'required',
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                "error" => "The request body is not valid"
            ], 422);
        }


        $user = User::query()->
            where('email', $request->login)->orWhere('phone', $request->login)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                "error"=> "Invalid credentials"
            ], 401);
        }

        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
        ], 200);
    }

    public function logout(Request $request){
        if($request->user()){
            $request->user()->tokens()->delete();
        }
        return response()->json(null, 204);
    }
}
