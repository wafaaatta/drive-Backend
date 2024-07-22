<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\ItemOrder;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    public function add(Request $request)
{
    $cart = Cart::where('user_id', 2)->first();
    if (!$cart) {
        $cart = new Cart([
            'user_id' => 1
        ]);
        $cart->save();
    }

    $product = Product::findOrFail($request->product_id);

    $itemOrder = $cart->itemOrders()->where('product_id', $product->id)->first();
    if ($itemOrder) {
        $itemOrder->quantity += $request->quantity;
    } else {
        $itemOrder = new ItemOrder([
            'product_id' => $product->id,
            'quantity' => $request->quantity,
            'price' => $product->price,
        ]);
        $cart->itemOrders()->save($itemOrder);
    }
    $itemOrder->save();

    return response()->json($cart->with('itemOrders', 'itemOrders.product')->get());
}

public function remove(Request $request)
{
    $cart = Cart::where('user_id', 2)->first();
    if (!$cart) {
        return response()->json(['error' => 'Cart not found'], 404);
    }

    $itemOrder = $cart->itemOrders()->where('product_id', $request->product_id)->first();
    if ($itemOrder) {
        if($itemOrder -> quantity == 1){
            $cart->itemOrders()->where('product_id', $request->product_id)->delete();
        } else {
            $itemOrder->quantity -= 1;
            $itemOrder->save();
        }
    }

    return response()->json($cart->load('itemOrders'));
}

public function removeItemOrder(Request $request)
{
    $cart = Cart::where('user_id', 2)->first();
    if (!$cart) {
        return response()->json(['error' => 'Cart not found'], 404);
    }

    $itemOrder = $cart->itemOrders()->where('product_id', $request->product_id)->first();
    if ($itemOrder) {
        $cart->itemOrders()->where('product_id', $request->product_id)->delete();
    }

    return response()->json($cart->load('itemOrders'));
}

public function clearCart(Request $request)
{
    $cart = Cart::where('user_id', 2)->first();
    if (!$cart) {
        return response()->json(['error' => 'Cart not found'], 404);
    }

    $cart->itemOrders()->delete();

    return response()->json($cart->load('itemOrders'));
}

public function view()
{
    if(!auth()->check()) {
        $cart = Cart::where('user_id', 2)->first();
        if (!$cart) {
            return response()->json(['error' => 'Cart not found'], 404);
        }

        return response()->json($cart->with('itemOrders', 'itemOrders.product')->get());
    }
    $cart = Cart::where('user_id', auth()->user()->id)->first();
    if (!$cart) {
        return response()->json(['error' => 'Cart not found'], 404);
    }

    return response()->json($cart);
}

    public function getCartItemsCount(Request $request)
    {
        $cart = Cart::where('user_id', 2)->first();
        if (!$cart) {
            return response()->json(['error' => 'Cart not found'], 404);
        }

        return response()->json($cart->itemOrders()->count());
    }

}
