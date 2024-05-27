<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PromotionController extends Controller
{
    public function index()
    {
        $promotions = Promotion::orderBy('created_at', 'desc')->get();
        return response([
            'message' => 'Promotions fetched successfully',
            'promotions' => $promotions->load('user', 'event'),
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            "event_id" => "required",
            "platform" => "in:social_media,email,referral,others",
        ]);
        $user = Auth::user();
        $promotion = Promotion::create([
            "event_id" => $request->event_id,
            "platform" => $request->platform,
            "user_id" => $user->id
        ]);

        return response([
            'message' => 'Promotion created successfully',
            'promotion' => $promotion
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            "event_id" => "required",
            "platform" => "in:social_media,email,referral,others",
        ]);

        $user = Auth::user();
        $promotion = Promotion::where('id', $id)->first();

        $promotion->update([
            "event_id" => $request->event_id,
            "platform" => $request->platform,
            "user_id" => $user->id
        ]);

        return response([
            'message' => 'Promotion updated successfully',
            'promotion' => $promotion
        ], 200);
    }

    public function show($id)
    {
        $promotion = Promotion::where('id', $id)->first();
        return response([
            'message' => 'Promotion fetched successfully',
            'promotion' => $promotion->with('event', 'user')->get(),
        ], 200);
    }
    
    public function destroy(Promotion $promotion)
    {
        $promotion->delete();
        return response([
            'message' => 'Promotion deleted successfully'
        ], 200);
    }
}
