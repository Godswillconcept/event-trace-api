<?php

namespace App\Http\Controllers;

use App\Models\Event;
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
}
