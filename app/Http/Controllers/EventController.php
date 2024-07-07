<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Faq;
use App\Models\Promotion;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::orderBy("created_at", "desc")->get();

        return response([
            "message" => "Events fetched successfully", "events" => $events->load('category', 'venue', 'organizer', 'tickets', 'faqs'),
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            "name" => "required",
            "description" => "required",
            "date_time" => "required",
            "guest_count" => "required",
            "category_id" => "numeric",
            "venue_id" => "numeric",
        ]);
        // dd($request->all());
        $currentUser = Auth::user();
        if ($currentUser->role === 'event_organizer') {
            $event = Event::create([
                "name" => $request->name,
                "description" => $request->description,
                "date_time" => $request->date_time,
                "guest_count" => $request->guest_count,
                "category_id" => $request->category_id,
                "venue_id" => $request->venue_id,
                "organizer_id" => $currentUser->id,
            ]);

            return response([
                "message" => "Event created successfully", "event" => $event,
            ], 200);
        } else {
            return response([
                "message" => "Only event organizers can create events",
            ], 403);
        }
    }

    public function show($slug)
    {
        $event = Event::where("slug", $slug)->with("category", "venue", 'organizer')->first();
        if (!$event) {
            return response([
                "message" => "Event not found",
            ], 404);
        }
        return response([
            "message" => "Event fetched successfully", "event" => $event,
        ], 200);
    }

    public function update(Request $request, $slug)
    {
        $event = Event::where("slug", $slug)->first();
        if (!$event) {
            return response([
                "message" => "Event not found",
            ], 404);
        }
        $user = Auth::user();
        $event->update(array_filter([
            "name" => $request->name,
            "description" => $request->description,
            "date_time" => $request->date_time,
            "guest_count" => $request->guest_count,
            "category_id" => $request->category_id,
            "venue_id" => $request->venue_id,
        ], function ($value) {
            return $value !== null; // Only update fields present in the request
        }));
        return response([
            "message" => "Event updated successfully", "event" => $event,
        ], 200);
    }

    public function destroy($slug)
    {
        $event = Event::where("slug", $slug)->first();
        if (!$event) {
            return response([
                "message" => "Event not found",
            ], 404);
        }
        $event->delete();
        return response([
            "message" => "Event deleted successfully",
        ], 200);
    }

    public function tickets($slug)
    {
        $event = Event::where("slug", $slug)->first();
        if (!$event) {
            return response([
                "message" => "Event not found",
            ], 404);
        }
        return response([
            "message" => "Event tickets fetched successfully", "tickets" => $event->tickets,
        ], 200);
    }

    public function bookings($slug)
    {
        $event = Event::where("slug", $slug)->first();
        if (!$event) {
            return response([
                "message" => "Event not found",
            ], 404);
        }

        return response([
            "message" => "Event bookings fetched successfully", "bookings" => $event->bookings,
        ], 200);
    }

    public function faqs($slug)
    {
        $event = Event::where("slug", $slug)->first();
        if (!$event) {
            return response([
                "message" => "Event not found",
            ], 404);
        }
        return response([
            "message" => "Event FAQs fetched successfully", "faqs" => $event->faqs,
        ], 200);
    }

    public function attachPhotos(Request $request, $slug)
    {
        $request->validate([
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $event = Event::where("slug", $slug)->first();

        if (!$event) {
            return response([
                "message" => "Event not found",
            ], 404);
        }
        if ($request->hasFile('photos')) {
            $imagePaths = [];
            foreach ($request->file('photos') as $key => $photo) {
                $imagePath = $this->saveImage($photo, 'events');
                $imagePaths[] = $imagePath;
            }
            $event->update([
                "photos" => $imagePaths,
            ]);
        }
        return response([
            "message" => "Event Images uploaded successfully", "event" => $event,
        ], 200);
    }


    public function attachTickets(Request $request, $slug)
    {
        $request->validate([
            "name" => "required",
            "description" => "required",
            "quantity" => "numeric||nullable",
            "price" => "required||nullable",
            "sales_start" => "required||nullable",
            "sales_end" => "required||nullable",
        ]);
        $event = Event::where("slug", $slug)->first();

        if (!$event) {
            return response([
                "message" => "Event not found",
            ], 404);
        }
        // dd($request->all());

        $ticket = Ticket::create([
            "name" => $request->name,
            "description" => $request->description,
            "event_id" => $event->id,
            "quantity" => $request->quantity,
            "price" => $request->price,
            "sales_start" => $request->sales_start,
            "sales_end" => $request->sales_end,
        ]);



        return response([
            "message" => "Event Tickets uploaded successfully", "ticket" => $ticket,
        ], 200);
    }

    public function attachFaqs(Request $request, $slug)
    {

        $request->validate([
            "questions" => "required|array",
            "questions.*" => "required|string",
            "answers" => "required|array",
            "answers.*" => "required|string",
        ]);
        // dd($request->all());

        $event = Event::where('slug', $slug)->first();
        if (!$event) {
            return response([
                'message' => 'No event found',
            ]);
        }

        $faq = Faq::create([
            "event_id" => $event->id,
            "questions" => $request->questions,
            "answers" => $request->answers,
        ]);

        return response([
            "message" => "FAQ added to event successfully",
            "faq" => $faq
        ], 200);
    }

    public function attachPromotions(Request $request, $slug)
    {
        $request->validate([
            "platform" => "in:social_media,email,referral,others",
        ]);
        $user = Auth::user();

        $event = Event::where('slug', $slug)->first();
        if (!$event) {
            return response([
                'message' => 'No event found',
            ]);
        }

        // dd($request->all());
        $promotion = Promotion::create([
            "event_id" => $event->id,
            "platform" => $request->platform,
            "user_id" => $user->id
        ]);

        return response([
            'message' => 'Promotion created successfully',
            'promotion' => $promotion
        ], 200);
    }
}
