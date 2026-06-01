<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivitylogsController extends Controller
{
    public function activitylogs(){
        $logs=Activity::with('causer','subject')
        ->where('subject_type', Asset::class)
        ->get();

        $formatedlogs=[];

        foreach($logs as $log){
            $formatedlogs[]=[
            'id'=>$log->id,
            'causer_id'=>$log->causer_id,
            'causer_name'=>$log->causer->name,
            'description'=>$log->description,
            'asset_id'=>$log->subject_id,
            'asset_name'=>$log->properties['attributes']['name'] ?? 'N/A',
            'created_at'=>$log->created_at->format('Y-m-d H:i:s')
            ];
        }
        return response()->json([
            'success'=>true,
            'data'=>$formatedlogs,
            'message'=>'Activity log retrieved'
        ]);
    }
}
