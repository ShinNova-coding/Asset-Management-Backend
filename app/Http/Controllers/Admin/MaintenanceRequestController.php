<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PermissionController;
use App\Models\Asset;
use App\Models\Category;
use App\Models\Maintenance;
use App\Models\MaintenanceRequest;
use DB;
use Exception;
use Illuminate\Http\Request;

class MaintenanceRequestController extends Controller
{
    //mainenance button clicked
    public function maintainRequest(Request $request, $id)
    {
        PermissionController::checkPermission('create-maintenance-requests');

        $request->validate([
            'issue_type' => 'required',
            'problem_description' => 'required',
            'evidence_image' => 'required'
        ]);

        $asset = Asset::where('asset_id', $id)->first();
        if (!$asset) {
            return response()->json([
                'success' => false,
                'message' => 'Asset not found'
            ], 404);
        }

        $maintenancerequest = DB::transaction(function () use ($request, $asset) {
            $maintenancerequest = Maintenance::create([
                'asset_id' => $asset->asset_id,
                'employee_id' => auth()->user()->employee_id,
                'category_id' => $asset->category_id,
                'issue_type' => $request->issue_type,
                'problem_description' => $request->problem_description,
                'status' => 'request'
            ]);

            if ($request->has('evidence_image') && $request->filled('evidence_image')) {
                $maintenancerequest->addMediaFromBase64($request->evidence_image)
                    ->toMediaCollection('evidences');
            }

            return $maintenancerequest;
        });

        $image_url = $maintenancerequest->getFirstMediaUrl('evidences') ?: null;
        $preview_url = $maintenancerequest->getFirstMediaUrl('evidences', 'preview') ?: null;

        $maintenancerequest->image_url = $image_url;
        $maintenancerequest->preview_url = $preview_url;

        return response()->json([
            'success' => true,
            'data' => $maintenancerequest,
            'message' => 'Maintenance request send successfully'
        ], 201);

    }

    //admin approve maintenance request
    public function approve(Request $request, $id)
    {
        PermissionController::checkPermission('approve-maintenance-requests');

        $maintenanceapprove = Maintenance::where('asset_id', $id)->first();

        if (!$maintenanceapprove) {
            return response()->json([
                'success' => false,
                'message' => 'Maintenance request not found'
            ], 404);
        }
        $maintenanceapprove->update([
            'status' => 'pending',
        ]);


        return response()->json([
            'success' => true,
            'data' => $maintenanceapprove,
            'message' => 'Maintenance request approved successfully'
        ]);
    }

    public function maintain(Request $request, $id)
    {
        PermissionController::checkPermission('update-maintenance-requests');
        try {
            $maintenance = Maintenance::where('asset_id', $id)->first();
            if (!$maintenance) {
                return response()->json(['success' => false, 'message' => 'Maintenance request not found'], 404);
            }

            $request->validate([
                'remark' => 'required|string',
            ]);

            $maintenance->update([
                'status' => 'maintenance',
                'remark' => $request->remark,
                'maintenance_date' => now()
            ]);

            Asset::where('asset_id', $maintenance->asset_id)->update(['status' => 'maintenance']);

            return response()->json([
                'success' => true,
                'data' => $maintenance,
                'message' => 'Maintenance request send successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function complete(Request $request, $id)
    {
        PermissionController::checkPermission('update-maintenance-requests');
        try {
            $maintenance = Maintenance::where('asset_id', $id)->first();
            if (!$maintenance) {
                return response()->json(['success' => false, 'message' => 'Maintenance not found'], 404);
            }

            $request->validate([
                'vendor' => 'required|string',
                'vendor_phno' => 'required|string',
                'vendor_address' => 'required|string',
                'cost' => 'required|integer',
            ]);
            $maintenance->update([
                'status' => 'completed',
                'completed_date' => now(),
                'vendor' => $request->vendor,
                'vendor_phno' => $request->vendor_phno,
                'vendor_address' => $request->vendor_address,
                'cost' => $request->cost,
            ]);
            Asset::where('asset_id', $maintenance->asset_id)->update(['status' => 'available']);
            return response()->json([
                'success' => true,
                'data' => $maintenance,
                'message' => 'Maintenance request completed successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function cancel($id)
    {
        PermissionController::checkPermission('cancel-maintenance-requests');
        try {
            $maintenance = Maintenance::where('asset_id', $id)->first();
            if (!$maintenance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Maintenance not found'
                ], 404);
            }
            $maintenance->update([
                'status' => 'canceled',
                'completed_date' => now()
            ]);
            Asset::where('asset_id', $maintenance->asset_id)->update(['status' => 'available']);
            return response()->json([
                'success' => true,
                'data' => $maintenance,
                'message' => 'Maintenance request canceled successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


}
