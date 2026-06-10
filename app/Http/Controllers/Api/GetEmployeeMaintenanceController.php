<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Maintenance;
use Exception;
use Illuminate\Http\Request;

class GetEmployeeMaintenanceController extends Controller
{
    public function getemployeemaintenance(Request $request)
    {
        PermissionController::checkPermission('view-maintenances');
        try{
        $id=$request->user()->id;  

        $maintenance=Maintenance::with('asset.category','asset.media')
                        ->where('users_id',$id)
                        ->where('status','active')
                        ->get()
                        ->pluck('asset')
                        ->map(function($asset){
                            if($asset){
                           $asset->image_url = $asset->getFirstMediaUrl('images') ?: null;

                           unset($asset->media);
                            }
                            return $asset;
                        });
            if($maintenance->isEmpty()){
                        return response()->json([
                            'success'=>false,
                            'message'=>'No active maintenance found for the employee'
                        ],404);
                    }

        return response()->json([
            'success'=>true,
            'data'=>$maintenance,
            'Message'=>'Asset retrieved according to employee maintenance'
        ]);

        }catch(Exception $e){
            return response()->json([
            'success'=>false,
            'message'=>$e->getMessage()     
        ]);

        }
    }
}
