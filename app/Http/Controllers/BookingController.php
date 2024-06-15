<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Photographer;
use App\Models\User;


class BookingController extends Controller
{
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

    public function getAllBooking(Request $request)
    {
        $user = $request->user(); 

        if ($user) {
            $token = $user->remember_token;
            
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = User::where('remember_token', $token)->first();
        $isPhotographer = Photographer::where('user_id', $user->id)->first();


        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        $request->query('status', null);
        
        // check is the user photographer or client
        if ($isPhotographer) {
           if ($request->status){
                $bookings = Booking::where('photographer_id', $isPhotographer->id)->where('status', $request->status)->get();
                $bookings['is_photographer'] = "YES I AM";
            } else {
                $bookings = Booking::where('photographer_id', $isPhotographer->id)->get();
                $bookings['is_photographer'] = "YES I AM";
            }
        } else {
            if ($request->status){
                $bookings = Booking::where('user_id', $user->id)->where('status', $request->status)->get();
            } else {
                $bookings = Booking::where('user_id', $user->id)->get();
            }
        }

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

        $user = $request->user(); 

        if ($user) {
            $token = $user->remember_token;
            
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = User::where('remember_token', $token)->first();

        // check valid total_harga
        $photographer = Photographer::find($request->photographer_id);
        if ($request->total_harga < $photographer->start_price || $request->total_harga > $photographer->end_price) {
            return response()->json([
                'message' => 'Total harga tidak valid'
            ], 400);
        }

        $input['user_id'] = $user->id;
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
        $input['nama_pemesan'] = $user->fullname;
        
        $photographer = Photographer::find($request->photographer_id);
        $name_photographer = User::find($photographer->user_id);

        $input['nama_photographer'] = $name_photographer->fullname;
        
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
            // check the price should be 10% of total_harga
            if ($request->price != $booking->total_dp){
                return response()->json([
                    'message' => 'Price not valid'
                ], 400);
            }

            $booking->status = 'proses';
        } else if ($booking->status == 'menunggu_pelunasan'){
            // check the price should be 90% of total_harga
            if ($request->price != $booking->total_harga - $booking->total_dp){
                return response()->json([
                    'message' => 'Price not valid'
                ], 400);
            }

            $booking->status = 'selesai';
            $booking->status_paid = true;
        } else {
            return response()->json([
                'message' => 'Booking not valid'
            ], 400);
        }

        $booking->save();
        return response()->json([
            'message' => 'Payment Success',
            'data' => $booking
        ]);
    }

    public function acceptOrder($booking_id, Request $request)
    {

        $request->validate([
            'confirmation' => 'required|boolean',
        ]);

        $booking = Booking::find($booking_id);
        if (!$booking) {
            return response()->json([
                'message' => 'Booking not found'
            ], 404);
        }

        if ($request->confirmation == true){
            $booking->status = 'menunggu_dp';
        }

        $booking->save();
        return response()->json([
            'message' => 'Success',
            'data' => $booking
        ]);
    }

    public function rejectOrder($booking_id, Request $request)
    {

        $request->validate([
            'confirmation' => 'required|boolean',
        ]);

        $booking = Booking::find($booking_id);
        if (!$booking) {
            return response()->json([
                'message' => 'Booking not found'
            ], 404);
        }

        if ($request->confirmation == false){
            $booking->status = 'ditolak';
            $booking->alasan_ditolak = $request->alasan_ditolak;
        } 

        $booking->save();
        return response()->json([
            'message' => 'Success',
            'data' => $booking
        ]);
    }



}
