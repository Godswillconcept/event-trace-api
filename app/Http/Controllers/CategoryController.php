<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::latest()->get();
        return response([
            'message' => 'Categories fetched successfully',
            "categories" => $categories
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            "name" => "required",
            "description" => "required",
        ]);



        $category = Category::create([
            "name" => $request->name,
            "description" => $request->description,
        ]);
        return response([
            'message' => 'Category created successfully',
            "category" => $category
        ], 200);
    }

    public function show($slug)
    {
        $category = Category::where("slug", $slug)->first();
        return response([
            'message' => 'Category fetched successfully',
            "category" => $category
        ], 200);
    }

    public function update(Request $request, $slug)
    {
        $request->validate([
            "name" => "required",
            "description" => "required",
        ]);
        $category = Category::where("slug", $slug)->first();
        if (!$category) {
            return response([
                "message" => "Category not found",
            ], 404);
        }
        $category->update([
            "name" => $request->name,
            "description" => $request->description,
        ]);
        return response([
            "message" => "Category updated successfully", "category" => $category,
        ], 200);
    }

    public function destroy($slug)
    {
        $category = Category::where("slug", $slug)->first();
        if (!$category) {
            return response([
                "message" => "Category not found",
            ], 404);
        }
        $category->delete();
        return response([
            "message" => "Category deleted successfully",
        ], 200);
    }
}
