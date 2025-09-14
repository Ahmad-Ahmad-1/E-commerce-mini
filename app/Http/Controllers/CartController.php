<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCartItemRequest;
use App\Http\Requests\UpdateCartItemQuantityRequest;
use App\Http\Resources\CartItemResource;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cart = Cart::firstOrCreate(['user_id' => $request->user()->id]);

        $cart->load('items.product');

        return response()->json([
            'items' => CartItemResource::collection($cart->items),
        ]);
    }

    public function add(AddCartItemRequest $request)
    {
        $productId = $request->validated('product_id');
        $quantity  = $request->validated('quantity');

        $product = Product::findOrFail($productId);

        $cart = Cart::firstOrCreate(['user_id' => $request->user()->id]);
        $cartItem = $cart->items()->firstOrNew(['product_id' => $productId]);
        $newQuantity = $cartItem->quantity + $quantity;

        if ($newQuantity > $product->quantity) {
            return response()->json([
                'message' => 'Not enough stock available.',
                'available_quantity' => $product->quantity - $cartItem->quantity,
            ], 400);
        }

        $cartItem->quantity = $newQuantity;
        $cartItem->save();

        $cart->load('items.product');

        return response()->json([
            'items' => CartItemResource::collection($cart->items),
        ]);
    }

    public function remove(Request $request, CartItem $item)
    {
        $item->delete();

        $cart = $request->user()
            ->cart()
            ->with('items.product')
            ->first();

        return response()->json([
            'items' => CartItemResource::collection($cart->items),
        ]);
    }

    public function updateQuantity(UpdateCartItemQuantityRequest $request, CartItem $item)
    {
        $quantity = $request->validated('quantity'); 
        
        if ($quantity > $item->product->quantity) {
            return response()->json([
                'message' => "Not enough stock available.",
                'available_quantity' => $item->product->quantity,
            ], 400);
        }

        $item->update([
            'quantity' => $quantity,
        ]);

        $cart = $item->cart()->with('items.product')->first();

        return response()->json([
            'items' => CartItemResource::collection($cart->items),
        ]);
    }
}
