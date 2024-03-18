<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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

        $isPhotographer = Photographer::where('user_id', $user->id)->exists();

    }
}
