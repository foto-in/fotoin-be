<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Photographer;
use App\Models\Portofolio;
use Illuminate\Support\Facades\Storage;


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
            'username' => 'required|string',
            'fullname' => 'required|string',
            'profile_image' => 'required|string',
        ]);

        // process profile_image
        if (!Storage::exists('public/users/')) {
            Storage::makeDirectory('public/users/');
        }

        $directory = 'public/users';

        $photoBase64 = $request->profile_image;
        $photo = base64_decode($photoBase64);

        // get photo mime type
        $f = finfo_open();
        $mime_type = finfo_buffer($f, $photo, FILEINFO_MIME_TYPE);
        finfo_close($f);

        // check the image is valid
        if (!in_array($mime_type, ['image/jpeg', 'image/png'])) {
            return response()->json([
                'message' => 'Photo must be jpeg or png'
            ], 400);
        }

        $photoName = str_replace("-", "_", $user->id) . '.' . explode('/', $mime_type)[1];
        Storage::put($directory . '/' . $photoName, $photo);

        // get public url photographer
        $profilePicture = asset(Storage::url($directory . '/' . $photoName));

        $username = $request->username;
        $fullname = $request->fullname;
        if ($isPhotographer) {
            $email = $request->email;
            $user->update([
                'username' => $username,
                'fullname' => $fullname,
                'email' => $email,
                'profile_image' => $profilePicture,
            ]);
            return response()->json([
                'message' => 'User updated successfully',
                'data' => $user
            ]);
        }

        $user->update([
            'username' => $username,
            'fullname' => $fullname,
            'profile_image' => $profilePicture,
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
            $photographer = $user->Photographer()->first();
            $user['photographer'] = $photographer;
            $first_portofolio = $photographer->Portofolio()->first();
            $user['photographer']['portofolio'] = $first_portofolio;
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
