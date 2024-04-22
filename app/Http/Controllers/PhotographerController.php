<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Photographer;
use App\Models\Portofolio;
use App\Http\Requests\PortofolioRequest;


class PhotographerController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|uuid|exists:users,id',
            'username' => 'required|string|max:50|unique:users',
            'fullname' => 'required|string|max:100',
            'email' => 'required|email|unique:photographers,email',
            'no_hp' => 'required|string',
            'no_telegram' => 'required|string',
            'type' => 'required|in:personal,tim',
            'specialization' => 'required|array',
            'specialization.*' => 'required|string',
            'camera' => 'required|array',
            'camera.*' => 'required|string',
        ]);

        if ($validator->fails()){
            return response()->json(['error' => $validator->errors()], 401);
        }

        $user = User::find($request->user_id);
        $user->username = $request->username;
        $user->fullname = $request->fullname;

        $input['user_id'] = $user->id;
        $input['email'] = $request->email;
        $input['no_hp'] = $request->no_hp;
        $input['no_telegram'] = $request->no_telegram;
        $input['type'] = $request->type;
        $input['specialization'] = $request->specialization;
        $input['camera'] = $request->camera;
        $photographer = Photographer::create($input);

        if ($photographer){
            return response()->json([
                'success' => true,
                'message' => 'Photographer created successfully',
                'data' => $photographer,
                'user' => $user
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Photographer could not be created'
            ]);
        }
    }

    public function uploadPortofolio(PortofolioRequest $request)
    {
        $photographer = Photographer::find($request->photographer_id);

        if (!$photographer){
            return response()->json([
                'success' => false,
                'message' => 'Photographer not found'
            ]);
        }

        // check folder photographer already exists
        if (!Storage::exists('public/portofolios/' . $photographer->id)){
            Storage::makeDirectory('public/portofolios/' . $photographer->id);
        }

        // array to store photo url
        $photos = [];

        // get directory name photographers
        $directory = 'public/portofolios/' . $photographer->id;

        // Upload photo to storage
        for ($i = 0; $i < count($request->photos); $i++){
            $photoBase64 = $request->photos[$i];
            $photo = base64_decode($photoBase64);

            // get photo mime type
            $f = finfo_open();
            $mime_type = finfo_buffer($f, $photo, FILEINFO_MIME_TYPE);
            finfo_close($f);

            // check the image is valid
            if (!in_array($mime_type, ['image/jpeg', 'image/png'])){
                return response()->json([
                    'success' => false,
                    'message' => 'Photo must be jpeg or png'
                ]);
            }

            $photoName = str_replace(" ", "_", $request->title) . '_' . $i . '.' . explode('/', $mime_type)[1];
            Storage::put($directory . '/' . $photoName, $photo);

            // get public url photo
            $photos[$i] = asset(Storage::url($directory . '/' . $photoName));

        }

        $portofolio = new Portofolio();
        $portofolio->photographer_id = $photographer->id;
        $portofolio->title = $request->title;
        $portofolio->description = $request->description;
        $portofolio->url_photo = $photos;
        $portofolio->save();

        return response()->json([
            'success' => true,
            'message' => 'Portofolio uploaded successfully',
            'data' => $portofolio
        ]);

    }

    public function searchPhotographer(Request $request)
    {
        $name = $request->query('name', null);
        $specialization = $request->query('specialization', null);

        $photographers = Photographer::query();

        if ($name){
            $photographers->whereHas('user', function($query) use ($name){
                $query->where('fullname', 'like', '%' . $name . '%');
            });
        }

        if ($specialization){
            $photographers->whereJsonContains('specialization', $specialization);
        }

        $photographers = $photographers->get();

        return response()->json([
            'success' => true,
            'message' => 'Photographer found',
            'data' => $photographers
        ]);
    }
}
