<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::orderBy("created_at", "desc")->get();
        return response([
            "message" => "Bookings fetched successfully",
            "bookings" => $bookings
        ], 200);
    }


    public function store(Request $request)
    {
        $request->validate([
            "event_id" => "numeric",
            'ticket_id' => "numeric",
            "quantity" => "numeric",
            'description' => "string||nullable",
            "price" => "numeric||nullable",
        ]);

        $user = Auth::user();
        $bookingPrice = Ticket::find($request->ticket_id)->price * $request->quantity;
        $bookingDescription =  $user->name . " has " . $request->quantity . " " . Str::plural('booking', $request->quantity) . " for the " . Event::find($request->event_id)->name . " at " . Ticket::find($request->ticket_id)->price * $request->quantity;
        $booking = new Booking([
            "event_id" => $request->event_id,
            "user_id" => $user->id,
            "ticket_id" => $request->ticket_id,
            "quantity" => $request->quantity,
            "description" => $bookingDescription,
            "price" => $bookingPrice,

        ]);

        $booking->save();
        return response([
            "message" => "Booking created successfully", "booking" => $booking,
        ], 200);
    }

    public function show($id)
    {
        $booking = Booking::where('id', $id)->with('event', 'user', 'ticket')->first();
        return response([
            "booking" => $booking
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $booking = Booking::where('id', $id)->first();
        $booking->update($request->all());
        return response([
            "message" => "Booking updated successfully", "booking" => $booking,
        ], 200);
    }

    public function delete($id)
    {

        $booking = Booking::where('id', $id)->first();
        $booking->delete();
        return response([
            "message" => "Booking deleted successfully",
        ], 200);
    }
}
