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
            'products' => ProductResource::collection(Product::paginate(10)),
        ]);
    }

    public function latestProducts()
    {
        return response()->json([
            'products' => Product::latestTenResource(),
        ]);
    }

    public function store(StoreProductRequest $request)
    {
        $product = new Product($request->safe()->except('image'));

        $category = Category::firstWhere('category_name', $request->safe()->input('category_name'));

        $product->category()->associate($category);

        if ($request->hasFile('image')) {
            $product->addMediaFromRequest('image')
                ->withResponsiveImages()
                ->toMediaCollection('images');
        }

        $product->save();

        return response()->json([
            'message' => 'A product has been created successfully.'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return response()->json([
            'product' => new ProductResource($product)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $category = Category::firstWhere('category_name', $request->safe()->input('category_name'));

        $product->category()->associate($category);

        $product->clearMediaCollection('images');

        $product->addMediaFromRequest('image')
            ->withResponsiveImages()
            ->toMediaCollection('images');

        $product->save();

        return response()->json([
            'message' => "Product has been updated successfully."
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
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
