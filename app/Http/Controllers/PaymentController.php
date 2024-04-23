<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function getTotalPrice($id)
    {
        $booking = Booking::find($id);
        if (!$booking) {
            return response()->json([
                'message' => 'Booking not found'
            ], 404);
        }

        $status = $booking->status;

        if ($status == 'menunggu_dp') {
            $data = array(
                'total_price' => $booking->total_dp,
                'status' => $status
            );
            return response()->json([
                'message' => 'Total Payment',
                'data' => $data
            ], 200);
        }

        if ($status == 'menunggu_pelunasan') {
            $data = array(
                'total_price' => $booking->total_harga - $booking->total_dp,
                'status' => $status
            );
            return response()->json([
                'message' => 'Total Payment',
                'data' => $data
            ], 200);
        }
    }

    public function createPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|uuid|exists:bookings,id',
            'total_payment' => 'required|numeric',
            'payment_method' => 'required|string',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $input['booking_id'] = $request->booking_id;
        $input['total_payment'] = $request->total_payment;
        $input['payment_method'] = $request->payment_method;
        $input['status'] = $request->status;
        $payment = Payment::create($input);

        if ($payment) {
            return response()->json([
                'success' => true,
                'message' => 'Payment created successfully',
                'data' => $payment
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Payment could not be created'
            ]);
        }
    }

    
}
