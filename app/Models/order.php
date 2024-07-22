<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory;
    protected $fillable =
        ['id','cart_id', 'user_id', 'status', 'total'];


   public function itemOrders()
   {
       return $this->hasMany(ItemOrder::class);
   }
   public function cart()
   {
       return $this->belongsTo(Cart::class);
   }

   public static function frequentlyBoughtTogether($productId, $limit = 2)
    {
        // Find orders that include the given product
        $ordersWithProduct = ItemOrder::where('product_id', $productId)->pluck('cart_id');

        // Find products that are bought together with the given product
        $frequentlyBoughtTogether = ItemOrder::whereIn('cart_id', $ordersWithProduct)
            ->where('product_id', '!=', $productId)
            ->select('product_id', DB::raw('count(*) as frequency'))
            ->groupBy('product_id')
            ->orderBy('frequency', 'desc')
            ->limit($limit)
            ->get();

        return $frequentlyBoughtTogether->pluck('product_id');
    }
}