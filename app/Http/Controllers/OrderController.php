<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function placeOrder(Request $request)
{
    $cart = Cart::where('user_id', 2)->first();
    if (!$cart || $cart->itemOrders->isEmpty()) {
        return response()->json(['error' => 'Cart is empty'], 400);
    }

    $order = new Order([
        'user_id' => 2,
        'total_price' => $cart->itemOrders->sum(function ($itemOrder) {
            return $itemOrder->price * $itemOrder->quantity;
        }),
    ]);
    $order->save();

    foreach ($cart->itemOrders as $itemOrder) {
        $order->itemOrders()->save($itemOrder);
    }

    $cart->itemOrders()->delete();

    return response()->json($order->load('itemOrders.product'));
}

}
