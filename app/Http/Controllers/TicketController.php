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
            "tickets" => $tickets->load('event')
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
