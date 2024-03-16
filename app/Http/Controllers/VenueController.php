<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VenueController extends Controller
{
    public function index()
    {
        $venues = Venue::latest()->get();
        return response([
            'message' => 'Venues fetched successfully',
            "venues" => $venues
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            "name" => "required",
            "address" => "required",
            "phone" => "required",
            "description" => "required",
            "capacity" => "required",
            "status" => "required",
        ]);

        if (Auth::user()->role !== 'venue_owner') {
            return response([
                "message" => "Unauthorized",
            ], 401);
        }

        $venue = Venue::create([
            "name" => $request->name,
            "address" => $request->address,
            "phone" => $request->phone,
            "description" => $request->description,
            "capacity" => $request->capacity,
            "status" => $request->status,
            "owner_id" => Auth::user()->id
        ]);

        return response([
            "message" => "Venue created successfully",
            "venue" => $venue
        ], 200);
    }


    public function show($slug)
    {
        $venue = Venue::where('slug', $slug)->with('owner', 'events')->first();
        return response([
            "venue" => $venue
        ], 200);
    }

    public function attachAmenities(Request $request, $slug)
    {
        $venue = Venue::where("slug", $slug)->first();

        $request->validate([
            'name.*' => 'required',
            'value.*' => 'required',
        ]);

        $amenities = [];
        foreach ($request->name as $key => $name) {
            $amenities[] = [
                'name' => $name,
                'value' => $request->value[$key],
            ];
        }

        $venue->amenities = $amenities;
        $venue->save();

        return response([
            'message' => 'Amenities attached successfully',
        ], 200);
    }

    public function attachPhotos(Request $request, $slug)
    {
        $venue = Venue::where('slug', $slug)->first();

        $request->validate([
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // dd($request->all());
        if ($request->hasFile('photo')) {
            
            $imagePaths = [];
            foreach ($request->file('photo') as $key => $photo) {
                $imagePath = $this->saveImage($photo, 'venues');
                $imagePaths[] = $imagePath;
            }

            $venue->photos = $imagePaths;
            // dd($venue->photos);
            $venue->save();
        }

        return response([
            'message' => 'Images uploaded successfully',
        ], 200);
    }

    public function update(Request $request, $slug)
    {
        $venue = Venue::where('slug', $slug)->first();
        $request->validate([
            'website' => 'required',
            'email' => 'required|email',
        ]);

        if (Auth::user()->role !== 'venue_owner' || Auth::user()->id !== $venue->owner_id) {
            return response([
                "message" => "Unauthorized",
            ], 401);
        }

        $venue->update($request->only('website', 'email', 'status'));

        return response([
            'message' => 'Venue updated successfully',
        ], 200);
    }



    public function events($slug)
    {
        $venue = Venue::where("slug", $slug)->first();
        if (!$venue) {
            return response([
                "message" => "Venue not found",
            ], 404);
        }
        return response([
            "message" => "Venue events fetched successfully", "events" => $venue->events,
        ], 200);
    }

    public function destroy($slug)
    {
        $venue = Venue::where('slug', $slug)->first();
        $venue->delete();

        return response([
            'message' => 'Venue deleted successfully',
        ], 200);
    }
}
