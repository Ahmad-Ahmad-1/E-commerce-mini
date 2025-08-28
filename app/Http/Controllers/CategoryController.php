<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;

class CategoryController extends Controller
{
    public function index()
    {
        return response()->json([
            'categories' => CategoryResource::collection(Category::paginate(10)),
        ]);
    }

    public function store(StoreCategoryRequest $request)
    {
        Category::create($request->safe()->all());

        return response()->json([
            'message' => 'A category has been created successfully'
        ]);
    }

    public function show(Category $category) {
        return response()->json([
            'category' => new CategoryResource($category),
            'categoryProducts' => $category->latestTenProducts(),
        ]);
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category->update($request->safe()->all());

        return response()->json([
            'message' => 'Category has been updated successfully.'
        ]);
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return response()->json([
            'message' => 'Category has been deleted successfully.'
        ]);
    }
}
