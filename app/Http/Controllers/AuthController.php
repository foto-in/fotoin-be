<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Models\Photographer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $user = User::where('username', $request->username)->first();
        if (!$user || !Hash::check($request->password, $user->password)){
            throw ValidationException::withMessages([
                'username' => ['The provided credentials are incorrect.'],
            ]);
        }

        $isPhotographer = Photographer::find($user->id);

        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;
        // saving token to database
        $user->remember_token = $token;
        $user->save();
        if ($isPhotographer){
            $photographer = Photographer::find($user->id)->user_id;
            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => $user,
                    'photographer' => $photographer,
                    'token' => $token
                ]
            ]);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => $user,
                    'token' => $token
                ]
            ]);
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:50|unique:users',
            'fullname' => 'required|string|max:100',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()){
            return response()->json(['error' => $validator->errors()], 401);
        }

        $input = $request->all();
        $user = User::create($input);
        $token = $user->createToken('auth_token')->plainTextToken;
        // saving token to database
        $user->remember_token = $token;
        $user->save();
        if ($user){
            $user['token'] = $token;
            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => $user
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'User could not be created'
            ]);
        }
    }

    public function logout(Request $request)
    {
        $token = $request->user()->token();
        $token->revoke();
        return response()->json([
            'success' => true,
            'message' => 'Logout successful'
        ]);
    }

}
