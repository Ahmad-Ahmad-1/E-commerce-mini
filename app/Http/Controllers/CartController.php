<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCartItemRequest;
use App\Http\Requests\UpdateCartItemQuantityRequest;
use App\Http\Resources\CartItemResource;
use App\Http\Resources\ProductResource;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        // Get validated input
        $productId = $request->validated('product_id');
        $quantity  = $request->validated('quantity');

        // Ensure the user has a cart
        $cart = Cart::firstOrCreate(['user_id' => $request->user()->id]);

        // Find existing cart item or create a new one
        $cartItem = $cart->items()->firstOrNew(['product_id' => $productId]);

        // Increment quantity
        $cartItem->quantity += $quantity;

        // Save the cart item
        $cartItem->save();

        // Load items with products
        $cart->load('items.product');

        // Return clean JSON using CartItemResource
        return response()->json([
            'items' => CartItemResource::collection($cart->items),
        ]);
    }

    public function remove(Request $request, CartItem $item)
    {
        $item->delete();

        // Reload cart with items + products
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
        $item->update([
            'quantity' => $request->validated('quantity'),
        ]);

        // Reload cart with items + products
        $cart = $item->cart()->with('items.product')->first();

        return response()->json([
            'items' => CartItemResource::collection($cart->items),
        ]);
    }
}
