<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AssetRequest;
use Illuminate\Http\Request;

class AssignmentRequestController extends Controller
{
    public function pendingRequests()
    {
    
        $pendingRequests = AssetRequest::where('status', 'requested')->get();

        if($pendingRequests->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No pending asset requests found'
            ], 404);
        }

         foreach ($pendingRequests as $request) {
            $request->asset_image_url = $request->asset ? $request->asset->getFirstMediaUrl('images') : null;
        }
        return response()->json([
            'success' => true,
            'data' => $pendingRequests,
            'message' => 'Pending asset requests retrieved successfully'
        ]);
    }
}
