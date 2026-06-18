<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\PermissionController;
use App\Models\Asset;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;

class AssetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        PermissionController::checkPermission('view-assets');
        try {

            $assets = Asset::with('category')->latest()->paginate(50);


            if ($assets->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No assets found'
                ], 404);
            }

            foreach ($assets as $asset) {
                $asset->image_url = $asset->getFirstMediaUrl('images') ?: null;
                $asset->preview_url = $asset->getFirstMediaUrl('images', 'preview') ?: null;
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
        PermissionController::checkPermission('create-assets');

        try {
            $request->validate([
                'asset_code' => 'required|string|unique:assets,asset_code',
                'name' => 'required|string|max:255',
                'serial_number' => 'required|string|unique:assets,serial_number',
                'purchased_date' => 'required|date',
                'warranty_period' => 'required|integer',
                'category_id' => 'required|exists:categories,id',
                'status' => 'required|string',
                'condition' => 'required|string',
                'model' => 'required|string',
                'ram_capacity' => 'required|string',
                'storage' => 'required|string',
                'image' => 'required|string'
            ]);

            $asset = DB::transaction(function () use ($request) {
                $asset = Asset::create([
                    'asset_code' => $request->asset_code,
                    'name' => $request->name,
                    'serial_number' => $request->serial_number,
                    'purchased_date' => $request->purchased_date,
                    'warranty_period' => $request->warranty_period,
                    'category_id' => $request->category_id,
                    'status' => $request->status,
                    'condition' => $request->condition,
                    'model' => $request->model,
                    'ram_capacity' => $request->ram_capacity,
                    'storage' => $request->storage
                ]);

                if ($request->has('image') && $request->filled('image')) {
                    $asset->addMediaFromBase64($request->image)
                        ->toMediaCollection('images');
                }
                return $asset;
            });
            $image_url = $asset->getFirstMediaUrl('images') ?: null;
            $preview_url = $asset->getFirstMediaUrl('images', 'preview') ?: null;

            $asset->image_url = $image_url;
            $asset->preview_url = $preview_url;
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
    public function show(Request $request)
    {
        PermissionController::checkPermission('view-assets');
        try {

            $id = $request->input('id');
            $asset = Asset::with('category')->firstWhere('id', $id);

            if (!$asset) {
                return response()->json([
                    'success' => false,
                    'message' => 'Asset not found'
                ], 404);
            }

            $image_url = $asset->getFirstMediaUrl('images') ?: null;
            $preview_url = $asset->getFirstMediaUrl('images', 'preview') ?: null;

            $asset->image_url = $image_url;
            $asset->preview_url = $preview_url;

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
    public function update(Request $request)
    {
        PermissionController::checkPermission('update-assets');
        try {
            $id = $request->input('id');
            $asset = Asset::firstWhere('id', $id);

            if (!$asset) {
                return response()->json([
                    'success' => false,
                    'message' => 'Asset not found'
                ], 404);
            }

            $request->validate([
                'name' => 'required|string|max:255',
                'serial_number' => [
                    'sometimes',
                    Rule::unique('assets', 'serial_number')->ignore($id),
                ],
                'purchased_date' => 'required|date',
                'warranty_period' => 'required|integer',
                'category_id' => 'required|exists:categories,id',
                'status' => 'required|string',
                'condition' => 'required|string',
                'image' => 'string',
                'ram_capacity' => 'required|string',
                'storage' => 'required|string',
            ]);

            if ($request->has('image') && $request->filled('image')) {
                $asset->clearMediaCollection('images');
                $asset->addMediaFromBase64($request->image)
                    ->toMediaCollection('images');
            }

            $asset->update($request->except('image'));

            $image_url = $asset->getFirstMediaUrl('images') ?: null;
            $preview_url = $asset->getFirstMediaUrl('images', 'preview') ?: null;

            $asset->image_url = $image_url;
            $asset->preview_url = $preview_url;

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
    public function destroy(Request $request)
    {
        PermissionController::checkPermission('delete-assets');
        try {
            $id = $request->input('id');
            $asset = Asset::firstWhere('id', $id);

            if (!$asset) {
                return response()->json([
                    'success' => false,
                    'message' => 'Asset not found'
                ], 404);
            }

            switch ($asset->status) {
                case 'assigned':
                    return response()->json([
                        'success' => false,
                        'message' => 'Asset is assigned'
                    ], 422);
                    break;
                case 'maintenance':
                    return response()->json([
                        'success' => false,
                        'message' => 'Asset is under maintenance'
                    ]);
                    break;
                default:
                    $asset->delete();

                    return response()->json([
                        'success' => true,
                        'message' => 'Asset Deleted Successfully'
                    ], 200);
                    break;
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function onlyTrashed()
    {
        PermissionController::checkPermission('view-assets');
        try {
            $trashAsset = Asset::onlyTrashed()->with('category')->latest()->get();
            return response()->json([
                'success' => true,
                'data' => $trashAsset,
                'message' => 'Trashed asset retrieved succesfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function restore(Request $request)
    {
        PermissionController::checkPermission('update-assets');
        try {
            $id = $request->input('id');
            $asset = Asset::onlyTrashed()->firstWhere('id', $id);
            if (!$asset) {
                return response()->json([
                    'success' => false,
                    'message' => 'No trashed asset found'
                ]);
            }

            $asset->restore();

            return response()->json([
                'success' => true,
                'message' => 'Trashed Asset restore successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
