<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use Illuminate\Http\Request;

class CategoryAssetController extends Controller
{
    public function categoryAsset(Request $request)
    {
        $categoryId = $request->input('category_id');

        $assets = Asset::where('category_id', $categoryId)->latest()->get();
        if ($assets->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'There is no asset related to this category'
            ]);

        }
        return response()->json([
            'success' => true,
            'data' => $assets,
            'message' => 'Assets related to categories are retrieved successfully'
        ]);


    }
}
