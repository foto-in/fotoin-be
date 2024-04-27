<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;


class UserController extends Controller
{
    public function getDetailUser($username)
    {
        $user = User::where('username', $username)->first();
        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Success',
            'data' => $user
        ]);
    }

    public function updateProfileUser(Request $request, $id)
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

        if ($user->id != $id) {
            return response()->json([
                'message' => 'You are not authorized to update this user'
            ], 401);
        }

        $user->update($request->all());

        return response()->json([
            'message' => 'User updated successfully',
            'data' => $user
        ]);
    }
}
