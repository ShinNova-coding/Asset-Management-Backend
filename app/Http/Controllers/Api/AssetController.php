<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use Exception;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            $assets = Asset::with('category')->latest()->get();

            if ($assets->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No assets found'
                ], 404);
            }
            return response()->json([
                'success' => true,
                'data' => $assets
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'asset_id' => 'required|string|unique:assets,asset_id',
                'name' => 'required|string|max:255',
                'serial_number' => 'required|string|unique:assets,serial_number',
                'purchased_date' => 'required|date',
                'warranty_expiry' => 'required|date|after_or_equal:purchased_date',
                'category_id' => 'required|exists:categories,id',
                'status' => 'required|string',
                'condition' => 'required|string',
            ]);

            $asset = Asset::create([
                'asset_id' => $request->asset_id,
                'name' => $request->name,
                'serial_number' => $request->serial_number,
                'purchased_date' => $request->purchased_date,
                'warranty_expiry' => $request->warranty_expiry,
                'category_id' => $request->category_id,
                'status' => $request->status,
                'condition' => $request->condition,
            ]);

            return response()->json([
                'success' => true,
                'data' => $asset,
                'message' => 'Asset Created Successfully'
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
    public function show(string $id)
    {
        try {

            $asset = Asset::with('category')->firstWhere('asset_id', $id);

            if (!$asset) {
                return response()->json([
                    'success' => false,
                    'message' => 'Asset not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $asset
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $asset = Asset::firstWhere('asset_id', $id);

            if (!$asset) {
                return response()->json([
                    'success' => false,
                    'message' => 'Asset not found'
                ], 404);
            }

            $request->validate([
                'name' => 'required|string|max:255',
                'serial_number' => 'required|unique:assets,serial_number,' . $id . ',asset_id',
                'purchased_date' => 'required|date',
                'warranty_expiry' => 'required|date|after_or_equal:purchased_date',
                'category_id' => 'required|exists:categories,id',
                'status' => 'required|string',
                'condition' => 'required|string',
            ]);

            $asset->update($request->all());

            return response()->json([
                'success' => true,
                'data' => $asset->refresh(),
                'message' => 'Asset Updated Successfully'
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
    public function destroy(string $id)
    {
        try {
            $asset = Asset::firstWhere('asset_id', $id);

            if (!$asset) {
                return response()->json([
                    'success' => false,
                    'message' => 'Asset not found'
                ], 404);
            }

            $asset->delete();

            return response()->json([
                'success' => true,
                'message' => 'Asset Deleted Successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
