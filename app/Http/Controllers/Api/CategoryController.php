<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Api\PermissionController;
use App\Models\Category;
use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        PermissionController::checkPermission('view-categories');
        $categories = Category::all();

        if($categories->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No categories found'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'data' => $categories,
            'message' => 'Categories retrieved successfully'
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        PermissionController::checkPermission('create-categories');
        try{
         $request->validate([
            'name' => 'required|string|unique:categories,name'
        ]);

        $category = Category::create($request->only(['name']));

        return response()->json([
            'success' => true,
            'data' => $category,
            'message' => 'Category created successfully'
        ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        PermissionController::checkPermission('view-categories');
        $id = $request->query('category_id');
         $category = Category::firstWhere('id', $id);
        $category = Category::firstWhere('id', $id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $category,
            'message' => 'Category retrieved successfully'
        ], 200);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        PermissionController::checkPermission('update-categories');
        try {
            $id = $request->input('category_id');
            $category = Category::firstWhere('id', $id);

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category not found'
                ], 404);
            }

            $request->validate([
                'name' => 'required|string|max:255|unique:categories,name,' . $id
            ]);

            $category->update($request->only(['name']));

            return response()->json([
                'success' => true,
                'data' => $category,
                'message' => 'Category updated successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        PermissionController::checkPermission('delete-categories'); 
        try {
            $id = $request->input('category_id');
            $category = Category::firstWhere('id', $id);

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category not found'
                ], 404);
            }


            if ($category->asset()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete category. linked to existing assets.'
                ], 400);
            }

            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }

    }
}
