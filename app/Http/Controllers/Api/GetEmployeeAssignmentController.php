<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\PermissionController;
use App\Models\Assignment;
use Exception;
use Illuminate\Http\Request;

class GetEmployeeAssignmentController extends Controller
{
    public function getEmployeeAsset(string $id){
        
        PermissionController::checkPermission('view-assignments');
        try{
        $assignment=Assignment::with('assets')
                            ->where('employee_id',$id)
                            ->where('status','active')
                            ->get()
                            ->pluck('assets');

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
