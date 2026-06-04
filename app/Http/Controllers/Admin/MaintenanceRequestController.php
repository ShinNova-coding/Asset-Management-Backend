<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\PermissionController;
use App\Models\Asset;
use App\Models\Maintenance;
use DB;
use Exception;
use Illuminate\Http\Request;

class MaintenanceRequestController extends Controller
{
    public function updateStatus(Request $request)
    {
        try {
            $assetId = $request->input('asset_id');
            $status = $request->input('status');

            $asset = Asset::where('asset_id', $assetId)->first();
            if (!$asset) {
                return response()->json(['success' => false, 'message' => 'Maintenance request not found'], 404);
            }

            switch ($status) {
                case 'requested':
                    PermissionController::checkPermission('create-maintenance-requests');

                    $maintenance = Maintenance::where('asset_id', $assetId)->first();

                    if ($maintenance) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Maintenance request already exists'
                        ], 400);
                    }
                    $request->validate([
                        'issue_type' => 'required',
                        'problem_description' => 'required',
                        'evidence_image' => 'required'
                    ]);

                    $asset = Asset::where('asset_id', $assetId)->first();
                    if (!$asset) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Asset not found'
                        ], 404);
                    }

                    $maintenance = DB::transaction(function () use ($request, $asset) {
                        $maintenancerequest = Maintenance::create([
                            'asset_id' => $asset->asset_id,
                            'employee_id' => auth()->user()->employee_id,
                            'category_id' => $asset->category_id,
                            'issue_type' => $request->issue_type,
                            'problem_description' => $request->problem_description,
                            'status' => 'requested'
                        ]);

                        if ($request->has('evidence_image') && $request->filled('evidence_image')) {
                            $maintenancerequest->addMediaFromBase64($request->evidence_image)
                                ->toMediaCollection('evidences');
                        }

                        return $maintenancerequest;
                    });

                    $maintenance->image_url = $maintenance->getFirstMediaUrl('evidences') ?: null;
                    $maintenance->preview_url = $maintenance->getFirstMediaUrl('evidences', 'preview') ?: null;

                    return response()->json([
                        'success' => true,
                        'data' => $maintenance,
                        'message' => 'Maintenance request send successfully'
                    ], 201);
                    break;

                case 'approved':
                    PermissionController::checkPermission('approve-maintenance-requests');
                    $maintenance = Maintenance::where('asset_id', $assetId)->first();
                    $maintenance->update([
                        'status' => 'pending',
                    ]);
                    break;

                case 'maintenance':
                    PermissionController::checkPermission('update-maintenance-requests');
                    $maintenance = Maintenance::where('asset_id', $assetId)->first();
                    $request->validate([
                        'remark' => 'required|string',
                    ]);

                    $maintenance->update([
                        'status' => 'maintenance',
                        'remark' => $request->remark,
                        'maintenance_date' => now()
                    ]);

                    Asset::where('asset_id', $maintenance->asset_id)->update(['status' => 'maintenance']);
                    break;

                case 'completed':
                    PermissionController::checkPermission('update-maintenance-requests');
                    $maintenance = Maintenance::where('asset_id', $assetId)->first();
                    $request->validate([
                        'vendor' => 'required|string',
                        'vendor_phno' => 'required|string',
                        'vendor_address' => 'required|string',
                        'cost' => 'required|integer',
                        'duration' => 'required|integer',
                        'payment' => 'required|string'
                    ]);

                    $maintenance->update([
                        'status' => 'completed',
                        'completed_date' => now(),
                        'vendor' => $request->vendor,
                        'vendor_phno' => $request->vendor_phno,
                        'vendor_address' => $request->vendor_address,
                        'cost' => $request->cost,
                        'duration' => $request->duration,
                        'payment' => $request->payment
                    ]);

                    Asset::where('asset_id', $maintenance->asset_id)->update(['status' => 'available']);
                    break;

                case 'canceled':
                    PermissionController::checkPermission('cancel-maintenance-requests');
                    $maintenance = Maintenance::where('asset_id', $assetId)->first();
                    $maintenance->update([
                        'status' => 'canceled',
                        'completed_date' => now()
                    ]);

                    Asset::where('asset_id', $maintenance->asset_id)->update(['status' => 'available']);
                    break;

                default:
                    return response()->json(['success' => false, 'message' => 'Invalid status type'], 400);
            }

            return response()->json([
                'success' => true,
                'data' => $maintenance,
                'message' => 'Maintenance request status updated successfully to ' . $status
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}