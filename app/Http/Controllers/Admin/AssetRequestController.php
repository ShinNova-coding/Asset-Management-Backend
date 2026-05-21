<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PermissionController;
use App\Models\Asset;
use App\Models\AssetRequest;
use App\Models\Assignment;
use App\Notifications\AssetApprovedNotification;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class AssetRequestController extends Controller
{
    public function store(Request $request){

        PermissionController::checkPermission('create-asset-requests');
        $request->validate([
            'asset_id'=>'required'
        ]);

        $isassigned=AssetRequest::where('asset_id',$request->asset_id)
                                ->where('employee_id',auth()->user()->employee_id)
                                ->where('status','approved')
                                ->exists();
        
        if($isassigned){
            return response()->json([
                'success'=>false,
                'message'=>'The asset is assigned'
            ]);
        }

        $asset=Asset::where('asset_id',$request->asset_id);
        $asset->update(['status'=>'pending']);

        $assetrequest=AssetRequest::create([
            'asset_id'=>$request->asset_id,
            'employee_id'=>auth()->user()->employee_id,
            'status'=>'pending'
        ]);

        return response()->json([
            'success'=>true,
            'data'=>$assetrequest,
            'message'=>'Asset request send successfully'
        ]);
    }

    public function approve(Request $request, $id)
    {
        PermissionController::checkPermission('approve-asset-requests');
        $assetRequest = AssetRequest::findOrFail($id);

        if ($assetRequest->status != 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Asset request is not pending',
                'data'=>$assetRequest->id
            ], 400);
        }

         $assignment = Assignment::create([
            'employee_id' => $assetRequest->employee_id,
            'asset_id' => $assetRequest->asset_id,
            'status' => 'assigned',
            'assign_date' => now(),
        ]);

        $assetRequest->user->notify(new AssetApprovedNotification($assignment));

        $assetRequest->update(['status' => 'approved']);

        $asset=Asset::where('asset_id',$assetRequest->asset_id)->firstOrFail();
        $asset->update(['status'=>'assigned']);

        return response()->json([
            'success' => true,
            'message' => 'Asset request approved successfully',
        ], 200);

    }
}
