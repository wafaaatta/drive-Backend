<?php

namespace App\Http\Controllers;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    //
    public function index()
    {
        // $categories = Category::with('parentCategory')->get();
        $categories = Category::where('parent_category', null)->get();
        return response()->json($categories, 200);
    }

    public function getSubcategories($categoryId)
    {
        $subcategories = Category::where('parent_category', $categoryId)->get();
        return response()->json($subcategories, 200);
    }

    public function create()
    {
        $categories = Category::with('parent_category')->all();
        return response()->json($categories, 201);
    }


    public function store(Request $request)
    {

        if ($request->has('subCategories') && !is_array($request->subCategories)) {
            $subCategories = json_decode($request->subCategories, true);
            $request->merge(['subCategories' => $subCategories]);
        }


        $validate = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'image' => 'required|image',
        ]);


        if ($validate->fails()) {
            return response(['message' => 'Validation error', 'errors' => $validate->errors()], 422);
        }
        $random = Str::random(10);
        $image_path = $request->file('image')->storeAs('public/categories', $request -> file('image')->getClientOriginalName());


        $category = Category::create([
            'name' => $request->name,
            'description' => $request->description,
            'image' => Storage::url($image_path),
            'parent_category' => $request->parent_category
        ]);


        return response()->json($category, 201);
    }


    public function show($id)
    {
        $category = Category::with('subCategories')->find($id);


        return response()->json($category, 201);
    }

    public function edit(Int $id)
    {
        $category = Category::findOrFail($id);

        return response()->json($category, 201);

    }

    public function update(Request $request, Int $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'subCategories'=> 'nullable|string|max:255',
        ]);
        $category = Category::findOrFail($id);

        $category->update($validatedData);

        return response()->json($validatedData, 201);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json($category, 201);
    }
}
