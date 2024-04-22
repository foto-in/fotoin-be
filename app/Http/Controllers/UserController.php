<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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

    public function updateDetailUser(Request $request, $username)
    {
        $user = User::where('username', $username)->first();
        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        $user->update($request->all());
        return response()->json([
            'message' => 'User updated successfully',
            'data' => $user
        ]);
    }
}
