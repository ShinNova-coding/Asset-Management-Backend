<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Maintenance;
use DB;
use Exception;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $maintenances = Maintenance::with('asset')->latest()->get();
        if ($maintenances->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No maintenances found'], 404);
        }
        return response()->json(['success' => true, 'data' => $maintenances], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public static function store(Request $request)
    {
        try {
            $request->validate([
                'asset_id' => 'required|exists:assets,asset_id',
                'employee_id'=>'required|exists:users,employee_id',
                'category_id'=>'required|exists:categories,category_id',
                'vendor' => 'required|string',
                'vendor_phno' => 'required|string',
                'vendor_address' => 'required|string',
                'cost' => 'required|integer',
                'remark'=>'required',
                'maintenance_date' => 'required|date',
                'status'=>'required',
                'image'=>'required'
            ]);

            
            $asset = Asset::where('asset_id', $request->asset_id)->first();
            if ($asset->status !== 'available') {
                return response()->json(['success' => false, 'message' => 'Asset not available for maintenance'], 400);
            }
            $maintenance=DB::transaction(function () use ($request){
            $maintenance = Maintenance::create([
                'asset_id' => $request->asset_id,
                'employee_id'=>$request->employee_id,
                'category_id'=>$request->category_id,
                'vendor' => $request->vendor,
                'vendor_phno' => $request->vendor_phno,
                'vendor_address' => $request->vendor_address,
                'cost' => $request->cost,
                'status' =>$request->status,
                'remark'=>$request->remark,
                'maintenance_date' => $request->maintenance_date
            ]);

            if($request->has('image')&&$request->filled('image')){
                $maintenance->addMediaFromBase64($request->image)
                            ->toMediaCollection('images');
            }

            return $maintenance;
            });

            $image_url = $maintenance->getFirstMediaUrl('images') ?: null;
            $preview_url = $maintenance->getFirstMediaUrl('images', 'preview') ?: null;

            $maintenance->image_url = $image_url;
            $maintenance->preview_url = $preview_url;

            $asset->update(['status' => 'maintenance']);
            return response()->json(['success' => true, 'data' => $maintenance], 201);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $maintenance = Maintenance::with('asset')->find($id);

            $image_url = $maintenance->getFirstMediaUrl('images') ?: null;
            $preview_url = $maintenance->getFirstMediaUrl('images', 'preview') ?: null;

            $maintenance->image_url = $image_url;
            $maintenance->preview_url = $preview_url;
            if (!$maintenance) {
                return response()->json(['success' => false, 'message' => 'Maintenance not found'], 404);
            }
            return response()->json(['success' => true, 'data' => $maintenance], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $maintenance = Maintenance::find($id);
            if (!$maintenance) {
                return response()->json(['success' => false, 'message' => 'Maintenance not found'], 404);
            }

            $request->validate([
                'asset_id' => 'required|exists:assets,asset_id',
                'employee_id'=>'required|exists:users,employee_id',
                'category_id'=>'required|exists:categories,category_id',
                'vendor' => 'required|string',
                'vendor_phno' => 'required|string',
                'vendor_address' => 'required|string',
                'cost' => 'required|integer',
                'status'=>'required',
                'maintenance_date' => 'required|date',
            ]);

            if ($request->has('image') && $request->filled('image')) {
                $maintenance->clearMediaCollection('images');
                $maintenance->addMediaFromBase64($request->image)
                    ->toMediaCollection('images');
            }

            $maintenance->update($request->except('image'));

             $image_url = $maintenance->getFirstMediaUrl('images') ?: null;
            $preview_url = $maintenance->getFirstMediaUrl('images', 'preview') ?: null;

            $maintenance->image_url = $image_url;
            $maintenance->preview_url = $preview_url;

            return response()->json(['success' => true, 'data' => $maintenance], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try{
            $maintenance=Maintenance::find($id);
            if(!$maintenance){
                return response()->json(['success' => false, 'message' => 'Maintenance not found'], 404);
            }
            Asset::where('asset_id', $maintenance->asset_id)->update(['status' => 'available']);
            $maintenance->delete();
            return response()->json(['success' => true, 'message' => 'Maintenance record deleted successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function complete($id)
    {
        try {
            $maintenance = Maintenance::find($id);
            if (!$maintenance) {
                return response()->json(['success' => false, 'message' => 'Maintenance not found'], 404);
            }
            $maintenance->update([
                'status' => 'completed',
                'completed_date' => now()
            ]);
            Asset::where('asset_id', $maintenance->asset_id)->update(['status' => 'available']);
            return response()->json(['success' => true, 'data' => $maintenance], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function cancel($id)
    {
        try {
            $maintenance = Maintenance::find($id);
            if (!$maintenance) {
                return response()->json(['success' => false, 'message' => 'Maintenance not found'], 404);
            }
            $maintenance->update([
                'status' => 'canceled',
                'completed_date' => now()
            ]);
            Asset::where('asset_id', $maintenance->asset_id)->update(['status' => 'available']);
            return response()->json(['success' => true, 'data' => $maintenance], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
} 
