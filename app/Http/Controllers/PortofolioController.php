<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Portofolio;
use App\Models\Photographer;
use App\Models\Booking;


class PortofolioController extends Controller
{
    public function getAllPortofolio($id)
    {
        $photographer = Photographer::find($id);
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

    public function getDetailPortofolio($photographer_id, $id)
    {
        $photographer = Photographer::find($photographer_id);
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


    public function acceptBooking($id){
        $booking = Booking::find($id);
        if (!$booking) {
            return response()->json([
                'message' => 'Booking not found'
            ], 404);
        }

        $booking->status = 'menunggu_dp';
        $booking->save();

        return response()->json([
            'message' => 'Booking accepted successfully',
            'data' => $booking
        ]);
    }
}
