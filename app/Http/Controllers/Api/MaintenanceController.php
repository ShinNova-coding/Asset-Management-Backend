<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Maintenance;
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
    public function store(Request $request)
    {
        try {
            $request->validate([
                'asset_id' => 'required|exists:assets,asset_id',
                'vendor' => 'required|string',
                'vendor_phno' => 'required|string',
                'vendor_address' => 'required|string',
                'cost' => 'required|integer',
                'maintenance_date' => 'required|date'
            ]);

            $asset = Asset::where('asset_id', $request->asset_id)->first();
            if ($asset->status !== 'available') {
                return response()->json(['success' => false, 'message' => 'Asset not available for maintenance'], 400);
            }
            $maintenance = Maintenance::create([
                'asset_id' => $request->asset_id,
                'vendor' => $request->vendor,
                'vendor_phno' => $request->vendor_phno,
                'vendor_address' => $request->vendor_address,
                'cost' => $request->cost,
                'status' => 'under-repair',
                'maintenance_date' => $request->maintenance_date
            ]);
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
                'vendor' => 'required|string',
                'vendor_phno' => 'required|string',
                'vendor_address' => 'required|string',
                'cost' => 'required|integer',
                'maintenance_date' => 'required|date',
            ]);

            $maintenance->update($request->only(['vendor', 'vendor_phno', 'vendor_address', 'cost', 'status', 'maintenance_date', 'completed_date']));

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
