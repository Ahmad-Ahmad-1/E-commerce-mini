<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Flasher\Toastr\Laravel\Facade\Toastr;
use GrahamCampbell\ResultType\Success;

class HomeController extends Controller
{
    //
    public function index()
    {
        return view('Admin.index');
    }
    
    public function home()
    {

        $products = Product::simplePaginate(8);

        return response()->json([
            'products' => $products,
        ]);

        // return view('home.index1',compact('products'));
    }

    public function login_home()
    {
        // $products = Product::all();
        // return view('home.index', compact('products'));

        $products = Product::simplePaginate(8);

        return response()->json([
            'products' => $products,
        ]);
    }

    public function product_details($id)
    {
        $product = Product::find($id);
        return view('home.product_details', compact('product'));
    }
    
    public function add_cart($id)
    {
        $cart = new Cart();
        $product = Product::find($id);
        if ($product->quantity <= 0) {
            flash()->error('The Product not found');
            return redirect()->back();
        }
        $user = Auth::user();
        $user_id = $user->id;
        $cart->user_id = $user_id;
        $product_id = $id;
        $cart->product_id = $product_id;
        $product->quantity -= 1;
        $product->save();
        flash()->success('Add Product to Cart');
        $cart->save();
        return redirect()->back();
    }
    public function get_invoice()
    {
        $user = Auth::User();
        $user_id = $user->id;
        $orders = Cart::where('user_id', $user_id)->get();
        $total_invoice = 0.0;
        foreach ($orders as $order) {
            $id = $order->product_id;
            $product = Product::find($id);
            $total_invoice += $product->price;
        }
        return $total_invoice;
    }
}
