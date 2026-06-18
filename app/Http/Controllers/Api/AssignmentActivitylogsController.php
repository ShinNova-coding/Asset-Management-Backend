<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AssetRequest;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class AssignmentActivitylogsController extends Controller
{
    public function assignmentActivitylogs(){
        $logs=Activity::with('causer','subject')
        ->where('subject_type', AssetRequest::class)
        ->get();

        if($logs->isEmpty()){
            return response()->json([
                'success'=>false,
                'message'=>'No Assignment Activity logs yet'
            ]);
        }

        $formatedlogs=[];

        foreach($logs as $log){
            $formatedlogs[]=[
            'id' => $log->id,
            'description' => $log->description, 
            'status' => $log->properties['status'] ?? 'unknown',
            'asset_name' => $log->properties['asset_name'] ?? 'N/A',
            'action_by' => $log->properties['action_by'] ?? ($log->causer ? $log->causer->name : 'System'),
            'created_at' => $log->created_at->format('Y-m-d H:i:s'),

            
            ];
        }
         return response()->json([
            'success'=>true,
            'data'=>$formatedlogs,
            'message'=>'Assignment Activity log retrieved'
        ]);
    }
}
