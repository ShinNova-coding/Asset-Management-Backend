<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\PermissionController;
use App\Models\Assignment;
use Illuminate\Http\Request;

class AssignmentHistoryController extends Controller
{
    public function assignmentHistory(Request $request){
        PermissionController::checkPermission('view-assignments');

        $user = $request->user()->employee_id;
        $assignment=Assignment::with('asset')->where('employee_id', $user)->latest()->get();

        if($assignment->isEmpty()){
            return response()->json([
                'success'=>false,
                'message'=>'No assignment found'
            ],404);
        }

        return response()->json([
            'success'=>true,
            'data'=>$assignment,
            'message'=>'Assignment retrieved successfully'
        ]);

    }
}
