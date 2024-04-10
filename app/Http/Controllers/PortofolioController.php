<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PortofolioController extends Controller
{
    public function getAllPortofolio($username)
    {
        $photographer = Photographer::where('username', $username)->first();
        if (!$photographer) {
            return response()->json([
                'message' => 'Photographer not found'
            ], 404);
        }

        $portofolios = Portofolio::where('photographer_id', $photographer->id)->get();
        return response()->json([
            'message' => 'Success',
            'data' => $portofolios
        ]);

    }

    public function getDetailPortofolio($username, $id)
    {
        $photographer = Photographer::where('username', $username)->first();
        if (!$photographer) {
            return response()->json([
                'message' => 'Photographer not found'
            ], 404);
        }

        $portofolio = Portofolio::where('photographer_id', $photographer->id)->where('id', $id)->first();
        if (!$portofolio) {
            return response()->json([
                'message' => 'Portofolio not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Success',
            'data' => $portofolio
        ]);
    }
}
