<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories=Category::all();
        return response()->json([
            'status' => 'success',
            'message' => 'Books retrieved successfully',
            'data' => $categories
        ], 200); // OK
    
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        $validatedRequest = $request->validated();
        $category = Category::create($validatedRequest);
        return response()->json([
           'status' => 'success',
           'message' => 'Category created successfully',
            'data' => $category
        ], 201); // Created
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json([
               'status' => 'error',
               'message' => 'Category not found'
            ], 404);
        }
        return response()->json([
           'status' => 'success',
           'message' => 'Category retrieved successfully',
            'data' => $category
        ], 200); // OK

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $validatedRequest = $request->validated();
        $category->update($validatedRequest);
        return response()->json([
           'status' => 'success',
           'message' => 'Category updated successfully',
            'data' => $category
        ], 200); // OK
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json([
               'status' => 'error',
               'message' => 'Category not found'
            ], 404);
        }
        $category->delete();
        return response()->json([
           'status' => 'success',
           'message' => 'Category deleted successfully'
        ], 200); // OK
    }
}
