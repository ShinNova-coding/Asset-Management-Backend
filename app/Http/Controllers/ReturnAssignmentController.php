<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Assignment;
use Exception;
use Illuminate\Http\Request;

class ReturnAssignmentController extends Controller
{
     public static function returnAssignment($id)
    {
        PermissionController::checkPermission('update-assignments');
        try {
            $assignment = Assignment::where('asset_id', $id)->where('status', 'active')->first();      
           
            if (!$assignment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Active assignment not found for this asset'
                ], 404);
            }

            $assignment->update([
                'status' => 'returned',
                'returned_date' => now()
            ]);

            Asset::where('asset_id', $assignment->asset_id)->update(['status' => 'available']);

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
