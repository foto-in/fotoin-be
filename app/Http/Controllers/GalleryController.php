<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Photographer;
use App\Models\User;
use App\Models\Gallery;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class GalleryController extends Controller
{

    public function uploadOrder($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'photos' => 'required|array',
            'photos.*' => 'required|string',
        ]);

        if ($validator->fails()){
            return response()->json(['error' => $validator->errors()], 401);
        }

        $booking = Booking::find($id);

        if (!$booking) {
            return response()->json([
                'message' => 'Booking not found'
            ], 404);
        }

        $photographer = Photographer::find($booking->photographer_id);
        $user = User::find($booking->user_id);

        // check folder gallery user exist or not
        if (!Storage::exists('public/gallery/'.$user->id)) {
            Storage::makeDirectory('public/gallery/'.$user->id);
        }

        // array to store photos url
        $photos = [];

        $directory = 'public/gallery/'. $user->id;

        // Upload photo to Storage
        for($i = 0; $i < count($request->photos); $i++) {
            $photoBase64 = $request->photos[$i];
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

            $photoName = str_replace(" ", "_", $photographer->fullname) . '_' . $i . '.' . explode('/', $mime_type)[1];
            Storage::put($directory . '/' . $photoName, $photo);

            // get public url photographer
            $photos[$i] = asset(Storage::url($directory . '/' . $photoName));
        }

        $gallery = new Gallery();
        $gallery->booking_id = $booking->id;
        $gallery->photographer_id = $photographer->id;
        $gallery->user_id = $user->id;
        $gallery->name_photographer = $photographer->fullname;
        $gallery->photos = $photos;
        $gallery->total_images = count($request->photos);
        $gallery->duration = 90;
        $gallery->save();

        return response()->json([
            'message' => 'Upload photo success',
            'data' => $gallery
        ]);

    }

    public function getDetailGallery($id)
    {
        $gallery = Gallery::find($id);

        if (!$gallery) {
            return response()->json([
                'message' => 'Gallery not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Success',
            'data' => $gallery
        ]);
    }

    public function getAllGallery($user_id)
    {
        $galleries = Gallery::where('user_id', $user_id)->get();

        return response()->json([
            'message' => 'Success',
            'data' => $galleries
        ]);
    }

    public function deleteGallery($id)
    {
        $gallery = Gallery::find($id);

        if (!$gallery) {
            return response()->json([
                'message' => 'Gallery not found'
            ], 404);
        }

        $gallery->delete();

        return response()->json([
            'message' => 'Gallery deleted successfully'
        ]);
    }

}
