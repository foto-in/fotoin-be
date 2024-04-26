<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Photographer;
use App\Models\User;


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
            'acara' => 'required|string',
            'lokasi' => 'required|string',
            'sesi_foto' => 'required|string',
            'tanggal_booking' => 'required|string',
            'durasi' => 'required|numeric',
            'konsep' => 'required|string',
            'total_harga' => 'required|numeric',
            'waktu_mulai' => 'required|string',
        ]);

        // check valid total_harga
        $photographer = Photographer::find($request->photographer_id);
        if ($request->total_harga < $photographer->start_price || $request->total_harga > $photographer->end_price) {
            return response()->json([
                'message' => 'Total harga tidak valid'
            ], 400);
        }

        $input['user_id'] = $request->user_id;
        $input['photographer_id'] = $request->photographer_id;
        $input['acara'] = $request->acara;
        $input['lokasi'] = $request->lokasi;
        $input['sesi_foto'] = $request->sesi_foto;
        $input['tanggal_booking'] = $request->tanggal_booking;
        $input['durasi'] = $request->durasi;
        $input['konsep'] = $request->konsep;
        $input['total_harga'] = $request->total_harga;
        $input['status'] = 'menunggu_konfirmasi';
        $input['total_dp'] = 10 * $request->total_harga / 100;
        $input['status_paid'] = false;
        $input['waktu_mulai'] = $request->waktu_mulai;
        
        $booking = Booking::create($input);
        return response()->json([
            'message' => 'Success',
            'data' => $booking
        ]);
    }

    public function payOrder($id, Request $request)
    {

        $request->validate([
            'price' => 'required|numeric',
            'method_payment' => 'required|string',
        ]);

        if ($request->method_payment != 'dummy'){
            return response()->json([
                'message' => 'Method payment not Available yet'
            ], 400);
        }



        $booking = Booking::find($id);
        if (!$booking) {
            return response()->json([
                'message' => 'Booking not found'
            ], 404);
        }


        if ($request->price != $booking->total_dp && $request->price != $booking->total_harga - $booking->total_dp){
            return response()->json([
                'message' => 'Price not valid'
            ], 400);
        }

        if ($booking->status == 'menunggu_dp'){
            $booking->status = 'proses';
        } else if ($booking->status == 'menunggu_pelunasan'){
            $booking->status = 'selesai';
        }

        $booking->save();
        return response()->json([
            'message' => 'Success',
            'data' => $booking
        ]);
    }



}
