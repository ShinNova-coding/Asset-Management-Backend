<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivitylogsController extends Controller
{
    public function activitylogs()
    {
        $logs = Activity::with('causer', 'subject')
            ->where('subject_type', Asset::class)
            ->latest()
            ->get();

        $formatedlogs = $logs->map(function ($log) {
            return [
                'id' => $log->id,
                'causer_id' => $log->causer_id,
                'causer_name' => $log->causer->name ?? 'System',
                'description' => $log->description,
                'asset_id' => $log->subject_id,
                'asset_name' => $log->subject->name 
                                ?? $log->properties['attributes']['name'] 
                                ?? $log->properties['old']['name'] 
                                ?? 'N/A',
                'old_status' => $log->properties['old']['status'] ?? null,
                'new_status' => $log->properties['attributes']['status'] ?? null,
                'created_at' => $log->created_at->format('Y-m-d H:i:s')
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formatedlogs,
            'message' => 'Activity log retrieved'
        ], 200);
    }
}