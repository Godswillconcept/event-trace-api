<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::orderBy("created_at", "desc")->get();
        return response([
            "message" => "Tickets fetched successfully",
            "tickets" => $tickets
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            "name" => "required",
            "description" => "required",
            "event_id" => "required",
            "quantity" => "numeric||nullable",
            "price" => "required||nullable",
            "sales_start" => "required||nullable",
            "sales_end" => "required||nullable",
        ]);

        $user = Auth::user();

        if ($user->role != 'event_organizer') {
            return response([
                'message' => 'No event organizer found',
            ]);
        }
        // dd($request->all());
        $ticket = Ticket::create([
            "name" => $request->name,
            "description" => $request->description,
            "event_id" => $request->event_id,
            "quantity" => $request->quantity,
            "price" => $request->price,
            "sales_start" => $request->sales_start,
            "sales_end" => $request->sales_end,
        ]);
        return response([
            "message" => "Ticket created successfully",
            "ticket" => $ticket
        ], 200);
    }

    public function show($id)
    {
        $ticket = Ticket::find($id);
        return response([
            "message" => "Ticket fetched successfully",
            "ticket" => $ticket
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $ticket = Ticket::find($id);

        $ticket->update($request->only('name', 'description', 'quantity', 'price', 'sales_start', 'sales_end'));
        return response([
            "message" => "Ticket updated successfully",
            "ticket" => $ticket
        ], 200);
    }

    public function destroy($id)
    {
        $ticket = Ticket::find($id);

        $ticket->delete();
        return response([
            "message" => "Ticket deleted successfully",
        ], 200);
    }
}
