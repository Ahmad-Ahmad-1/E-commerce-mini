<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return response()->json([
            'products' => Product::latestTenResource(),
        ]);
    }

    public function latestProducts()
    {
        return response()->json([
            'products' => Product::latestTenResource(),
        ]);
    }

    public function myProducts()
    {
        return response()->json([
            'products' => ProductResource::collection(
                request()->user()->products()->with('categories')->latest()->paginate(10)
            )
        ]);
    }

    public function store(StoreProductRequest $request)
    {
        $product = new Product($request->safe()->except('image'));

        $product->user()->associate($request->user());
        
        $product->save();

        $categoryNames = $request->input('category_name');
        $categories = Category::whereIn('category_name', $categoryNames)->pluck('id')->toArray();
        $product->categories()->sync($categories);

        $product->addMediaFromRequest('image')
            ->withResponsiveImages()
            ->toMediaCollection('images');

        return response()->json([
            'message' => 'A product has been created successfully.'
        ]);
    }

    public function show(Product $product)
    {
        return response()->json([
            'product' => new ProductResource($product)
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $categoryNames = $request->input('category_name');
        $categories = Category::whereIn('category_name', $categoryNames)->pluck('id')->toArray();
        $product->categories()->sync($categories);

        $product->clearMediaCollection('images');
        $product->addMediaFromRequest('image')
            ->withResponsiveImages()
            ->toMediaCollection('images');

        $product->update($request->safe()->all());

        return response()->json([
            'message' => "Product has been updated successfully."
        ]);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json([
            'message' => 'Product has been deleted successfully.'
        ]);
    }

    public function search(Request $request)
    {
        $search = $request->query('search');

        if (!$search) {
            return response()->json([
                'message' => 'Please provide a search term.',
            ], 422);
        }

        $products = Product::where('title', 'like', "%$search%")->paginate(10);

        if ($products->isEmpty()) {
            return response()->json([
                'message' => 'There are no items that match your search.',
            ]);
        }

        return response()->json([
            'products' => ProductResource::collection($products),
        ]);
    }
}
