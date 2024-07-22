<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\Tag;
use Illuminate\Support\Facades\Validator;

//use GuzzleHttp\Handler\Proxy;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::all();

        foreach ($products as $product) {
            if ($product->categories) {
                $product->category = $product->categories->pluck('name');
                unset($product->categories);
            };
        }
        return response()->json($products);
    }

    public function getProductsByCategory($categoryId)
    {
        $products = Product::where('category_id', $categoryId)->get();
        return response()->json($products, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'description' => 'string',
            'image' => 'sometimes|image',
            'price' => 'required',
            'stock' => 'sometimes',
            "category_id" => "required",
        ]);

        if ($validate->fails()) {
            return response(['message' => 'Validation error', 'errors' => $validate->errors()], 422);
        }

        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'category_id' => $request->category_id,
            "image" => "/storage/categories/carlos-muza-hpjSkU2UYSU-unsplash.jpg"
        ]);

        if (!empty($request->image)) {
            $product->image = $request->image;
            $this->storeImage($product);
        }

        return response()->json([
            'product' => $product,
            'message' => 'Le produit ' . $product->name . ' a été créé avec succès'
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(String $id)
    {
        $product = Product::find($id);

        if ($product->categories) {
            $product->category = $product->categories->pluck('name');
            unset($product->categories);
        }
        return response()->json($product);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $id)
    {
        $product = Product::find($id);
        return response()->json($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, String $id)
    {
        if ($request->has('categories') && !is_array($request->categories)) {
            $categories = json_decode($request->categories, true);
            $request->merge(["categories" => $categories]);
        }

        $request->validate([
            'name' => 'required|max:255',
            'description' => 'string',
            'image' => 'sometimes|image',
            'price' => 'required',
            'categories' => 'sometimes|array',
            'stock' => 'sometimes',
        ]);

        $product = Product::find($id);
        $product->update($request->all());

        if (!empty($request->categories)) {
            $product->categories()->attach($categories);
        }

        $this->storeImage($product);

        return response()->json([
            'product' => $product,
            'message' => 'Le produit ' . $product->name . ' a été mis à jour !'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(String $id)
    {
        $product = Product::find($id);
        if ($product->categories) {
            $product->categories()->detach();
        };
        // if ($product->image) {
        //     $product->image->delete();
        // }
        $product->delete();

        return response()->json([
            'message' => 'Le produit ' . $product->name . ' a été supprimé !'
        ], 200);
    }

    public function addTags(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);
        $tags = $request->input('tags', ['food']);

        foreach ($tags as $tagName) {
            $tag = Tag::firstOrCreate(['name' => $tagName]);
            $product->tags()->attach($tag->id);
        }

        return response()->json($product->tags, 200);
    }

    public function getSimilarProducts($productId)
    {
        $product = Product::with('tags')->findOrFail($productId);
        $tagNames = $product->tags->pluck('name')->toArray();

        $similarProducts = Product::withSimilarTags($tagNames)->where('id', '!=', $productId)->get();

        return response()->json($similarProducts, 200);
    }

    public function frequentlyBoughtTogether($productId)
    {
        $productIds = Order::frequentlyBoughtTogether($productId);

        $products = Product::whereIn('id', $productIds)->get();

        return response()->json($products, 200);
    }

    private function storeImage(Product $product)
    {
        if (request()->hasFile('image')) {
            $originalFilename = request()->file('image')->getClientOriginalName();
            $imagePath = request()->file('image')->storeAs('images', $originalFilename, 'public');
            $product->image = 'storage/' . $imagePath;
            $product->save();
        }
    }
}