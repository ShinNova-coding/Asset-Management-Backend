<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\PermissionController;
use App\Models\Assignment;
use Exception;
use Illuminate\Http\Request;

class GetEmployeeAssignmentController extends Controller
{
    public function getEmployeeAsset(Request $request){
        
        PermissionController::checkPermission('view-assignments');
        try{
        $id = $request->user()->id;
        $assignment=Assignment::with('asset.category','asset.media')
                            ->where('users_id',$id)
                            ->where('status','active')
                            ->whereHas('asset')
                            ->get()
                            ->pluck('asset')
                            ->map(function($asset){
                                if($asset){
                               $asset->image_url = $asset->getFirstMediaUrl('images') ?: null;

                               unset($asset->media);
                                }
                                return $asset;
                            });


    if($assignment->isEmpty()){
                            return response()->json([
                                'success'=>false,
                                'message'=>'No active assignments found for the employee'
                            ],404);
                        }

        return response()->json([
            'success'=>true,
            'data'=>$assignment,
            'Message'=>'Asset retrieved according to employee assign'
        ]);
        }catch(Exception $e){
            return response()->json([
                'success'=>false,
                'message'=>$e->getMessage()
            ]);
        }
    }
}
