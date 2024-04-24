<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::orderBy("created_at", "desc")->get();

        return response([
            "message" => "FAQs fetched successfully",
            "faqs" => $faqs
        ], 200);
    }

    public function store(Request $request, $slug)
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
            "message" => "FAQ created successfully",
            "faq" => $faq
        ], 200);
    }

    public function update(Request $request, $slug)
    {

        $request->validate([
            "questions" => "required|array",
            "questions.*" => "required|string",
            "answers" => "required|array",
            "answers.*" => "required|string",
        ]);


        $event = Event::where('slug', $slug)->first();
        if (!$event) {
            return response([
                'message' => 'No event found',
            ]);
        }

        $faq = Faq::where('event_id', $event->id)->first();
        if (!$faq) {
            return response([
                'message' => 'No FAQ found',
            ]);
        }

        $faq->update($request->only('questions', 'answers'));

        return response([
            "message" => "FAQ updated successfully",
            "faq" => $faq
        ], 200);
    }

    public function destroy($slug)
    {

        $event = Event::where('slug', $slug)->first();
        if (!$event) {
            return response([
                'message' => 'No event found',
            ]);
        }

        $faq = Faq::where('event_id', $event->id)->first();
        if (!$faq) {
            return response([
                'message' => 'No FAQ found',
            ]);
        }

        $faq->delete();

        return response([
            "message" => "FAQ deleted successfully",
        ], 200);
    }
}
