<?php

namespace App\Http\Controllers;

use App\Models\ItemOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ItemOrderController extends Controller
{
    public function index()
    {
        $items = ItemOrder::with('product','cart')->get();
        return response()->json($items);
    }

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            "product_id"=> "required",
            "cart_id" => "required",
        ]);

        if($validate->fails()){
            return response()->json(["message"=> $validate->errors()], 500);
        }

        $item_order = ItemOrder::create([
            "product_id"=> $request->product_id,
            "cart_id"=> $request->cart_id,
            "quantity" => 1
        ]);

        return response()->json([
            "message"=> "Item added to cart",
            "item_order"=> $item_order
        ],200);
    }
}
