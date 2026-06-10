<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\PermissionController;
use App\Models\Asset;
use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Exception;

class AssignmentController extends Controller
{
   
    public function index()
    {
        PermissionController::checkPermission('view-assignments');
        $assignments = Assignment::with(['asset'])
                                  ->where('status','active')
                                  ->latest()->get();
                 
        if ($assignments->isEmpty()) {
            return response()->json([
                'success' => false, 
                'message' => 'No assignments found'
                ], 404);
        }
        return response()->json([
            'success' => true,
            'data' => $assignments,
            'message' => 'Assignments retrieved successfully'
        ], 200);
        

        }

    public function store(Request $request)
    {
        PermissionController::checkPermission('create-assignments');
        try {
            $request->validate([
                'assets_id' => 'required|exists:assets,id',
                'users_id' => 'required|exists:users,id',
                'assigned_date' => 'required|date',
            ]);

            $asset = Asset::where('id', $request->assets_id)->first();
            if ($asset->status !== 'available') {
                return response()->json(['success' => false, 'message' => 'Asset not available'], 400);
            }

            $assignment = Assignment::create([
                'assets_id' => $request->assets_id,
                'users_id' => $request->users_id,
                'assigned_date' => $request->assigned_date,
                'status' => 'active'
            ]);

            $asset->update(['status' => 'assigned']);

            return response()->json(['success' => true, 'data' => $assignment], 201);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function show(Request $request, )
    {
        PermissionController::checkPermission('view-assignments');
        try {
            $id = $request->input('assignment_id');
            $assignment = Assignment::with(['asset', 'user'])->find($id);
            if (!$assignment) {
                return response()->json(['success' => false, 'message' => 'Assignment not found'], 404);
            }
            return response()->json(['success' => true, 'data' => $assignment], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        PermissionController::checkPermission('update-assignments');
        try {
            $id = $request->input('assignment_id');
            $assignment = Assignment::find($id);
            if (!$assignment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Assignment not found'
                ], 404);
            }
            $request->validate([
                'users_id' => 'exists:users,id',
                'assets_id' => 'exists:assets,id',
                'assigned_date' => 'date'
            ]);
            $assignment->update($request->only(['users_id', 'assets_id', 'assigned_date']));
            return response()->json([
                'success' => true,
                'data' => $assignment,
                'message' => 'Assignment Updated Successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);

        }
    }

    public function destroy(Request $request)
    {
        PermissionController::checkPermission('delete-assignments');
        try {
            $id = $request->input('assignment_id');
            $assignment = Assignment::findOrFail($id);

            if ($assignment->status === 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete active assignment. Please release the asset first.'
                ], 400);
            }
            Asset::where('id', $assignment->assets_id)->update(['status' => 'available']);
            $assignment->delete();
            return response()->json(['success' => true, 'message' => 'Deleted and Asset released']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    

   
    
}