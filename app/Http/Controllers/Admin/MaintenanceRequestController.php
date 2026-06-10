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
            $assetId = $request->input('assets_id');
            $status = $request->input('status');

            $asset = Asset::find($assetId);
            if (!$asset) {
                return response()->json(['success' => false, 'message' => 'Asset not found'], 404);
            }

            switch ($status) {
                case 'requested':
                    PermissionController::checkPermission('create-maintenance-requests');

                    if ($asset->status === 'maintenance') {
                        return response()->json(['success' => false, 'message' => 'Asset is already in maintenance'], 400);
                    }

                    $exists = Maintenance::where('assets_id', $assetId)
                        ->whereNotIn('status', ['completed', 'canceled'])
                        ->exists();

                    if ($exists) {
                        return response()->json(['success' => false, 'message' => 'An active request already exists'], 400);
                    }

                    $request->validate([
                        'issue_type' => 'required',
                        'problem_description' => 'required',
                        'evidence_image' => 'required'
                    ]);

                    $maintenance = DB::transaction(function () use ($request, $asset) {
                        $m = Maintenance::create([
                            'assets_id' => $asset->id,
                            'users_id' => auth()->id(),
                            'categories_id' => $asset->category_id,
                            'issue_type' => $request->issue_type,
                            'problem_description' => $request->problem_description,
                            'status' => 'requested'
                        ]);
                        if ($request->filled('evidence_image')) {
                            $m->addMediaFromBase64($request->evidence_image)->toMediaCollection('evidences');
                        }
                        return $m;
                    });
                    
                    return response()->json(['success' => true, 'data' => $maintenance, 'message' => 'Request sent successfully'], 201);

                case 'approved':
                case 'maintenance':
                case 'completed':
                case 'canceled':
                    $maintenance = Maintenance::where('assets_id', $assetId)
                        ->whereNotIn('status', ['completed', 'canceled'])
                        ->latest()
                        ->first();

                    if (!$maintenance) {
                        return response()->json(['success' => false, 'message' => 'No active maintenance request found'], 404);
                    }

                    return $this->handleUpdate($maintenance, $status, $request);

                default:
                    return response()->json(['success' => false, 'message' => 'Invalid status type'], 400);
            }

        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function handleUpdate($maintenance, $status, $request)
    {
        switch ($status) {
            case 'approved':
                PermissionController::checkPermission('approve-maintenance-requests');
                $maintenance->update(['status' => 'pending']);
                break;

            case 'maintenance':
                PermissionController::checkPermission('update-maintenance-requests');
                $request->validate(['remark' => 'required']);
                $maintenance->update(['status' => 'maintenance', 'remark' => $request->remark, 'maintenance_date' => now()]);
                Asset::where('id', $maintenance->assets_id)->update(['status' => 'maintenance']);
                break;

            case 'completed':
                PermissionController::checkPermission('update-maintenance-requests');
                $request->validate([
                    'vendor' => 'required',
                    'vendor_phno' => 'required',
                    'vendor_address' => 'required',
                    'cost' => 'required|integer',
                    'duration' => 'required|integer',
                    'payment' => 'required'
                ]);
                $maintenance->update(array_merge($request->only(['vendor', 'vendor_phno', 'vendor_address', 'cost', 'duration', 'payment']), [
                    'status' => 'completed', 'completed_date' => now()
                ]));
                Asset::where('id', $maintenance->assets_id)->update(['status' => 'available']);
                break;

            case 'canceled':
                PermissionController::checkPermission('cancel-maintenance-requests');
                $maintenance->update(['status' => 'canceled', 'completed_date' => now()]);
                Asset::where('id', $maintenance->assets_id)->update(['status' => 'available']);
                break;
        }

        return response()->json(['success' => true, 'data' => $maintenance, 'message' => 'Status updated to ' . $status]);
    }
}