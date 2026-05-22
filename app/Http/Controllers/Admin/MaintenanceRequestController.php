<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PermissionController;
use App\Models\Asset;
use App\Models\Maintenance;
use App\Models\MaintenanceRequest;
use Illuminate\Http\Request;

class MaintenanceRequestController extends Controller
{
    //mainenance button clicked
    public function maintainRequest(Request $request){
        PermissionController::checkPermission('create-maintenance-requests');

        $request->validate([
            'asset_id'=>'required'
        ]);

        $ismaintained=MaintenanceRequest::where('asset_id',$request->asset_id)
                                  ->where('employee_id',auth()->user()->employee_id)
                                  ->where('status','maintained')
                                  ->exists();

        if($ismaintained){
            return response()->json([
                'success'=>false,
                'message'=>'The asset is already assigned'
            ]);
        }

        $asset=Asset::where('asset_id',$request->asset_id)->get();
        $asset->update(['status','maintain']);

        $category_id = Asset::where('asset_id', $request->asset_id)
                            ->value('category_id');

        $maintanence=MaintenanceRequest::create([
            'asset_id'=>$request->asset_id,
            'employee_id'=>auth()->user()->employee_id,
            'category_id'=>$category_id,
            'status'=>'pending'
        ]);

        return response()->json([
            'success'=>true,
            'data'=>$maintanence,
            'message'=>'Maintenance Request '
        ]);
    }

    //admin approve maintenance request
    public function approve(Request $request,$id){
        PermissionController::checkPermission('approve-maintenance-requests');

        $maintenancerequest=MaintenanceRequest::findOrFail($id);

        if($maintenancerequest->status!='pending'){
            return response()->json([
                'success'=>false,
                'message'=>'Maintenance request is not pending'
            ],404);
        }

        $request->validate([
            'vendor'=>'required',
            'vendor_phno'=>'required',
            'vendor_address'=>'required|string',
            'cost' => 'required|integer',
            'remark'=>'required',
            'maintenance_date' => 'required|date',
            'status'=>'required',
            'image'=>'required'
        ]);

        $maintenance=Maintenance::create([
            'asset_id'=>$maintenancerequest->asset_id,
            'employee_id'=>$maintenancerequest->employee_id,
            'category_id'=>$maintenancerequest->category_id,
            
            'status'=>'maintenance',


        ]);
    }

}
