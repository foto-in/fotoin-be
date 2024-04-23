<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function getAllBookingPhotographer($username)
    {
        $photographer = Photographer::where('username', $username)->first();
        if (!$photographer) {
            return response()->json([
                'message' => 'Photographer not found'
            ], 404);
        }

        $bookings = Booking::where('photographer_id', $photographer->id)->get();
        return response()->json([
            'message' => 'Success',
            'data' => $bookings
        ]);
    }

    public function getDetailBookingPhotographer($username, $id)
    {
        $photographer = Photographer::where('username', $username)->first();
        if (!$photographer) {
            return response()->json([
                'message' => 'Photographer not found'
            ], 404);
        }

        $booking = Booking::where('photographer_id', $photographer->id)->where('id', $id)->first();
        if (!$booking) {
            return response()->json([
                'message' => 'Booking not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Success',
            'data' => $booking
        ]);
    }

    public function getAllBookingUser($username)
    {
        $user = User::where('username', $username)->first();
        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        $bookings = Booking::where('user_id', $user->id)->get();
        return response()->json([
            'message' => 'Success',
            'data' => $bookings
        ]);
    }

    public function getDetailBookingUser($username, $id)
    {
        $user = User::where('username', $username)->first();
        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        $booking = Booking::where('user_id', $user->id)->where('id', $id)->first();
        if (!$booking) {
            return response()->json([
                'message' => 'Booking not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Success',
            'data' => $booking
        ]);
    }

    public function createBooking(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'photographer_id' => 'required',
            'acara' => 'required',
            'lokasi' => 'required',
            'sesi_foto' => 'required',
            'tanggal_booking' => 'required',
            'durasi' => 'required',
            'konsep' => 'required',
            'total_harga' => 'required',
        ]);

        // check valid total_harga
        $photographer = Photographer::find($request->photographer_id);
        if ($request->total_harga < $photographer->start_price || $request->total_harga > $photographer->end_price) {
            return response()->json([
                'message' => 'Total harga tidak valid'
            ], 400);
        }

        $booking = Booking::create($request->all());
        $booking->total_dp = 10 * $booking->total_harga / 100;
        $booking->status = 'menunggu_konfirmasi';
        return response()->json([
            'message' => 'Success',
            'data' => $booking
        ]);
    }

    public function changeStatusBooking($id, Request $request)
    {
        $booking = Booking::find($id);
        if (!$booking) {
            return response()->json([
                'message' => 'Booking not found'
            ], 404);
        }

        $booking->status = $request->status;
        $booking->save();
        return response()->json([
            'message' => 'Success',
            'data' => $booking
        ]);
    }



}
