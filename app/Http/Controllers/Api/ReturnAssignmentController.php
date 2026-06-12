<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetRequest;
use App\Models\Assignment;
use Exception;
use Illuminate\Http\Request;

class ReturnAssignmentController extends Controller
{
         public static function returnAssignment(Request $request)
    {
        PermissionController::checkPermission('update-assignments');
        try {
            $id = $request->input('asset_id');
            $assignment = Assignment::where('assets_id', $id)->where('status', 'active')->first();      
           
            if (!$assignment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Active assignment not found for this asset'
                ], 404);
            }
            $asset=Asset::where('id', $assignment->assets_id)->first();
            if($asset->status=='maintainance'){
                return response()->json([
                    'success' => false,
                    'message' => 'Asset is under maintainance'
                ], 400);
            }

            $assignment->update([
                'status' => 'returned',
                'returned_date' => now()
            ]);

            Asset::where('id', $assignment->assets_id)->update(['status' => 'available']);

            return response()->json([
                'success' => true,
                'message' => 'Asset returned successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

}
