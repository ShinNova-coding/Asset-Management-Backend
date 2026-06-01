<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\PermissionController;
use App\Models\Asset;
use App\Models\AssetRequest;
use App\Models\Assignment;
use App\Notifications\AssetApprovedNotification;
use App\Notifications\AssetRejectNotification;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class AssetRequestController extends Controller
{
    //user ka assign button click yin
    public function assignRequest(Request $request,$id){

        PermissionController::checkPermission('create-asset-requests');
        $asset=Asset::where('asset_id',$id)->first();
        if(!$asset){
            return response()->json([
                'success'=>false,   
                'message'=>'Asset not found'
            ],404);
        }

        $isassigned=AssetRequest::where('asset_id',$asset->asset_id)
                                ->where('employee_id',auth()->user()->employee_id)
                                ->where('status','approved')
                                ->exists();
        
        if($isassigned){
            return response()->json([
                'success'=>false,
                'message'=>'The asset is assigned'
            ]);
        }

        $asset->update(['status'=>'pending']);

        $assetrequest=AssetRequest::create([
            'asset_id'=>$asset->asset_id,
            'employee_id'=>auth()->user()->employee_id,
            'status'=>'requested'
        ]);

        return response()->json([
            'success'=>true,
            'data'=>$assetrequest,
            'message'=>'Asset request send successfully'
        ]);
    }

    

    //admin ka approve button click yin
    public function approve(Request $request, $id)
    {
        PermissionController::checkPermission('approve-asset-requests');
        $assetRequest = AssetRequest::where('asset_id', $id)->first();
        if(!$assetRequest){
            return response()->json([
                'success' => false,
                'message' => 'Asset request not found',
            ], 404);
        }

        if ($assetRequest->status != 'requested') {
            return response()->json([
                'success' => false,
                'message' => 'Asset request is not available',
                'data'=>$assetRequest->id
            ], 400);
        }

         $assignment = Assignment::create([
            'employee_id' => $assetRequest->employee_id,
            'asset_id' => $assetRequest->asset_id,
            'status' => 'active',
            'assigned_date' => now(),
        ]);

        $assetRequest->update(['status' => 'approved']);

        $asset=Asset::where('asset_id',$assetRequest->asset_id)->firstOrFail();
        $asset->update(['status'=>'assigned']);

        return response()->json([
            'success' => true,
            'message' => 'Asset request approved successfully',
        ], 200);

    }

    public function cancel(Request $request, $id)
    {
        PermissionController::checkPermission('cancel-asset-requests');
        $assetRequest = AssetRequest::where('asset_id', $id)->first();

        if ($assetRequest->status != 'requested') {
            return response()->json([
                'success' => false,
                'message' => 'Asset request is not available',
                'data'=>$assetRequest->id
            ], 400);
        }


        $assetRequest->update(['status' => 'cancel']);

        $asset=Asset::where('asset_id',$assetRequest->asset_id)->firstOrFail();
        $asset->update(['status'=>'available']);

        return response()->json([
            'success' => true,
            'message' => 'Asset request canceled successfully',
        ], 200);

    }
}
