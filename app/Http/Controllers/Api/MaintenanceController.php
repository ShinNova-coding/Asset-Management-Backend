<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\PermissionController;
use App\Models\Asset;
use App\Models\Asset_record;
use App\Models\Assignment;
use App\Models\Expense;
use App\Models\Maintenance;
use App\Models\User;
use App\Services\FirebaseNotificationService;
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
        PermissionController::checkPermission('view-maintenances');
        $maintenances = Maintenance::with('asset', 'user', 'category')->latest()->get();
        if ($maintenances->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No maintenances found'], 404);
        }
        foreach ($maintenances as $maintenance) {
            $maintenance->accepted_by=User::find($maintenance->accepted_by);
            $image_url = $maintenance->getFirstMediaUrl('evidences') ?: null;
            $preview_url = $maintenance->getFirstMediaUrl('evidences', 'preview') ?: null;

            $maintenance->image_url = $image_url;
            $maintenance->preview_url = $preview_url;
        }
        return response()->json(['success' => true, 'data' => $maintenances], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public static function store(Request $request)
    {
        PermissionController::checkPermission('create-maintenances');
        try {
            $request->validate([
                'assets_id' => 'required|exists:assets,id',
                'users_id' => 'required|exists:users,id',
                'categories_id' => 'required|exists:categories,id',
                'issue_type' => 'required|string',
                'problem_description' => 'required|string',
                'remark' => 'required',
                'maintenance_date' => 'required|date',
                'status' => 'required',
                'evidence_image' => 'required'
            ]);


            $asset = Asset::where('id', $request->assets_id)->first();
            if ($asset->status !== 'available') {
                return response()->json(['success' => false, 'message' => 'Asset not available for maintenance'], 400);
            }
            $maintenance = DB::transaction(function () use ($request) {
                $maintenance = Maintenance::create([
                    'assets_id' => $request->assets_id,
                    'users_id' => $request->users_id,
                    'categories_id' => $request->categories_id,
                    'issue_type' => $request->issue_type,
                    'problem_description' => $request->problem_description,
                    'vendor' => $request->vendor,
                    'vendor_phno' => $request->vendor_phno,
                    'vendor_address' => $request->vendor_address,
                    'cost' => $request->cost,
                    'status' => $request->status,
                    'remark' => $request->remark,
                    'duration' => $request->duration,
                    'payment' => $request->payment,
                    'maintenance_date' => $request->maintenance_date
                ]);

                if ($request->has('evidence_image') && $request->filled('image')) {
                    $maintenance->addMediaFromBase64($request->image)
                        ->toMediaCollection('evidences');
                }

                return $maintenance;
            });

            $image_url = $maintenance->getFirstMediaUrl('evidences') ?: null;
            $preview_url = $maintenance->getFirstMediaUrl('evidences', 'preview') ?: null;

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
    public function show(Request $request)
    {
        PermissionController::checkPermission('view-maintenances');
        
        try {
            $id = $request->input('maintenance_id');
            $maintenance = Maintenance::with('asset', 'user', 'category')->find($id);

            if (!$maintenance) {
                return response()->json([
                 'success' => false,
                 'data' => $id,
                 'message' => 'Maintenance not found'], 404);
            }

            $image_url = $maintenance->getFirstMediaUrl('evidences') ?: null;
            $preview_url = $maintenance->getFirstMediaUrl('evidences', 'preview') ?: null;

            $maintenance->image_url = $image_url;
            $maintenance->preview_url = $preview_url;

            return response()->json(['success' => true, 'data' => $maintenance], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FirebaseNotificationService $firebaseService)
    {
        PermissionController::checkPermission('update-maintenances');
        
        try {
            $id = $request->input('maintenance_id');
            $maintenance = Maintenance::find($id);
            if (!$maintenance) {
                return response()->json(['success' => false, 'message' => 'Maintenance not found'], 404);
            }
            
            $request->validate([
                'completed_date' => 'required|date',
                'vendor' => 'required|string',
                'vendor_phno' => 'required|string',
                'vendor_address' => 'required|string',
                'cost' => 'required|numeric',
                'payment' => 'required|string',
                'duration' => 'required|numeric',
            ]);
            

            $maintenance->update([
                'status' => 'returned',
                'completed_date' => $request->completed_date,
                'vendor' => $request->vendor,
                'vendor_phno' => $request->vendor_phno,
                'vendor_address' => $request->vendor_address,
                'cost' => $request->cost,
                'payment' => $request->payment,
                'duration' => $request->duration
            ]);

            Expense::create([
                'users_id' => auth()->id(),
                'assets_id' => $maintenance->assets_id,
                'maintenance_id' => $maintenance->id,
                'cost' => $maintenance->cost,
                'title' => 'Maintenance',
                'expense_date' => $maintenance->completed_date,
                'expense_type' => 'maintenance',
                'status' => 'approved',
            ]);

            ReturnAssignmentController::returnAssignment($request);
            Asset::where('id', $maintenance->assets_id)->update(['status' => 'available']);
            
            Asset_record::create([
                    'assets_id' => $maintenance->assets_id,
                    'users_id' => auth()->id(),
                    'status' => 'maintenance_returned'  
                ]);

            $image_url = $maintenance->getFirstMediaUrl('images') ?: null;
            $preview_url = $maintenance->getFirstMediaUrl('images', 'preview') ?: null;

            $maintenance->image_url = $image_url;
            $maintenance->preview_url = $preview_url;

            $user=User::find($maintenance->users_id);
            
            if($user&& $user->fcm_token){
               $firebaseService->send(
                $user->fcm_token, 
                'Maintenance', 
                'Maintenance has been returned'.' '.$maintenance->asset->name);
 
            }
            
            return response()->json([
                'success' => true,
                'data' => $maintenance,
                'message' => 'Maintenance updated successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        PermissionController::checkPermission('delete-maintenances');
        try {
            $id = $request->input('maintenance_id');
            
            $maintenance = Maintenance::find($id);

            if (!$maintenance) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Maintenance not found'], 404);
            }

            if($maintenance->status=='requested'||$maintenance->status=='approved'){
                return response()->json([
                    'success' => false, 
                    'message' => 'Maintenance is only allowed to delete if it is returned'
                    ], 422);
            }
            
            Asset::where('id', $maintenance->assets_id)->update(['status' => 'available']);
           
            $maintenance->delete();

            return response()->json([
                'success' => true, 
                'message' => 'Maintenance record deleted successfully'
                ], 200);

        } catch (Exception $e) {

            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
                ], 500);
        }
    }


}
