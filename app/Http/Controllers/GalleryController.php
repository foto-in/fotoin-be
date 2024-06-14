<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Photographer;
use App\Models\User;
use App\Models\Gallery;
use Illuminate\Support\Facades\Storage;
use App\Models\Preview;
use Illuminate\Support\Facades\Validator;


class GalleryController extends Controller
{

    public function uploadOrder($booking_id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'photos' => 'required|array',
            'photos.*' => 'required|string',
        ]);

        if ($validator->fails()){
            return response()->json(['error' => $validator->errors()], 401);
        }

        $booking = Booking::find($booking_id);

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

        Storage::makeDirectory('public/gallery/'.$user->id.'/'. $booking->id);

        $directory = 'public/gallery/'. $user->id . '/' . $booking->id;

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
        $gallery->name_photographer = User::find($photographer->user_id)->fullname;
        $gallery->photos = $photos;
        $gallery->total_images = count($request->photos);
        $gallery->duration = 90;
        $gallery->save();

        $booking->status = 'menunggu_pelunasan';
        $booking->save();

        return response()->json([
            'message' => 'Upload photo success',
            'data' => $gallery
        ]);

    }

    public function uploadPreview($id, Request $request)
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

        $photographer = Photographer::find($booking->photographer_id)->first();
        $user = User::find($booking->user_id);

        // check folder gallery photographer exist or not
        if (!Storage::exists('public/preview/'.$user->id)) {
            Storage::makeDirectory('public/preview/'.$user->id);
        }

        // array to store photos url
        $photos = [];

        Storage::makeDirectory('public/preview/'.$user->id.'/'. $booking->id);

        $directory = 'public/preview/'. $user->id . '/' . $booking->id;

        // Upload photo to Storage
        for($i = 0; $i < count($request->photos) && $i < 5; $i++) {
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

        $preview = new Preview();
        $preview->booking_id = $booking->id;
        $preview->photographer_id = $photographer->id;
        $preview->photos = $photos;
        $preview->save();

        return response()->json([
            'message' => 'Upload photo success',
            'data' => $preview
        ]);
    }

    public function getPreviewGallery($booking_id)
    {
        $preview = Preview::where('booking_id', $booking_id)->first();

        if (!$preview) {
            return response()->json([
                'message' => 'Preview not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Success',
            'data' => $preview
        ]);
    }



    public function getDetailGallery(Request $request, $booking_id)
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

        $user_id = $user->id;

        $booking = Booking::find($booking_id);

        // check if the order has been paid
        if ($booking->status != 'selesai') {
            return response()->json([
                'message' => 'Order has not been completed'
            ], 400);
        }

        $gallery = Gallery::where('booking_id', $booking_id)->first();

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

    public function getAllGallery(Request $request)
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

        $user_id = $user->id;

        $galleries = Gallery::where('user_id', $user_id)->get();

        $return = [];

        $i = 0;
        foreach ($galleries as $gallery) {
            $booking = Booking::find($gallery->booking_id);

            // check if the order has been paid
            if ($booking->status != 'selesai') {
                continue;
            }

            $return[$i] = $gallery;
            $i++;
        }

        return response()->json([
            'message' => 'Success',
            'data' => $return
        ]);
    }

    public function deleteGallery($booking_id, Request $request)
    {

        $user = $request->user(); 

        if ($user) {
            $token = $user->remember_token;
            
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // check if the user is photographer or client
        $user = User::where('remember_token', $token)->first();
        $isPhotographer = Photographer::where('user_id', $user->id)->first();
        if ($isPhotographer) {
            return response()->json([
                'message' => 'Photographer cannot delete gallery'
            ], 401);
        }

        // check if the user is the owner of the gallery
        if ($user->id != $booking->user_id) {
            return response()->json([
                'message' => 'You are not authorized to delete this gallery'
            ], 401);
        }

        $gallery = Gallery::where('booking_id', $booking_id)->first();

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
