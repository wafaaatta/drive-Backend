<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FeedbackController extends Controller
{
    public function index()
    {
        return Feedback::all();
    }

    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'feedback' => 'required',
        'product_id' => 'required',
        'rating' => 'required|numeric|min:1|max:5',
        'user_id' => 'required',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 400);
    }

    $feedback = new Feedback();
    $feedback->feedback = $request->feedback;
    $feedback->product_id = $request->product_id;
    $feedback->rating = $request->rating;
    $feedback->user_id = $request->user_id;
    $feedback->save();

    // Fetch the product
    $product = Product::find($request->product_id);

    if ($product) {
        // Get the total rating and count of feedbacks
        $totalFeedbacks = $product->feedbacks()->count();
        $currentTotalRating = $product->feedbacks()->sum('rating');

        // Calculate the new average rating
        $newAverageRating = ($currentTotalRating + $request->rating) / ($totalFeedbacks + 1);

        // Update the product rating
        $product->rating = $newAverageRating;
        $product->save();
    }

    return response()->json($feedback, 201);
}


    public function getProductFeedbacks($id)
    {
        return Feedback::where('product_id', $id)->get();
    }

    public function getUserFeedbacks()
    {
        // $id = auth()->user()->id;
        $id = 1;
        return Feedback::where('user_id', $id)->get();
    }
}
