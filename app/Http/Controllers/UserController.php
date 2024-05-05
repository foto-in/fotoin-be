<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;


class UserController extends Controller
{
    public function updateProfileUser(Request $request)
    {
        $user = $request->user();

        if ($user) {
            $token = $user->remember_token;

        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = User::where('remember_token', $token)->first();

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        $isPhotographer = $user->Photographer()->exists();

        $request->validate([
            'username' => 'required',
            'fullname' => 'required',
        ]);

        $username = $request->username;
        $fullname = $request->fullname;
        if ($isPhotographer) {
            $email = $request->email;
            $user->update([
                'username' => $username,
                'fullname' => $fullname,
                'email' => $email
            ]);
            return response()->json([
                'message' => 'User updated successfully',
                'data' => $user
            ]);
        }

        $user->update([
            'username' => $username,
            'fullname' => $fullname,
        ]);

        return response()->json([
            'message' => 'User updated successfully',
            'data' => $user
        ]);
    }

    public function getProfileUser(Request $request)
    {
        $user = $request->user();

        if ($user) {
            $token = $user->remember_token;
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = User::where('remember_token', $token)->first();

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        $isPhotographer = $user->Photographer()->exists();

        if ($isPhotographer) {
            return response()->json([
                'message' => 'Success',
                'data' => $user,
                'role' => 'photographer'
            ]);
        } else {
            return response()->json([
                'message' => 'Success',
                'data' => $user,
                'role' => 'user'
            ]);
        }

    }
}
