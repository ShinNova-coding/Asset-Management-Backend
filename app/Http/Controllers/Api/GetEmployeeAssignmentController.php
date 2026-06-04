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
        $id = $request->user()->employee_id;
        $assignment=Assignment::with('asset.category','asset.media')
                            ->where('employee_id',$id)
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
                            

        return response()->json([
            'success'=>false,
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
