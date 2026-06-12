<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\PermissionController;
use App\Models\Asset;
use App\Models\Asset_record;
use App\Models\Assignment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Exception;

class AssignmentController extends Controller
{
   
    public function index()
    {
        PermissionController::checkPermission('view-assignments');
        $assignments = Assignment::with(['asset','user'])
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
                'assets_name' => 'required|exists:assets,name',
                'users_name' => 'required|exists:users,name',
                'note' => 'nullable|string',
            ]);

            $asset = Asset::where('name', $request->assets_name)->first();
            if ($asset->status !== 'available') {
                return response()->json(['success' => false, 'message' => 'Asset not available'], 400);
            }
            $asset_id=$asset->id;
            $users_id = User::where('name', $request->users_name)->first()->id;

             $assignment = DB::transaction(function () use ($request, $asset_id, $users_id) {
                $assignment = Assignment::create([
                    'assets_id' => $asset_id,
                    'users_id' => $users_id,
                    'note' => $request->note,
                    'assigned_date' => now(),
                    'status' => 'active'
                ]);

            Asset_record::create([
                'assets_id' => $asset_id,
                'users_id' => $users_id,
                'status' => 'asset_assigned',
            ]);

            return $assignment;
        });

            $asset->update(['status' => 'assigned']);

            return response()->json(['success' => true, 'data' => $assignment], 201);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function show(Request $request)
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
                'assets_code' => 'exists:assets,asset_code',
                'note' => 'nullable|string',
                'assigned_date' => 'date'
            ]);
            $asset=Asset::where('asset_code', $request->assets_code)->first();
            $assignment->update([
                'assets_id' => $asset->id,
                'note' => $request->note,
                'assigned_date' => $request->assigned_date
            ]);

            $asset->update(['status' => 'assigned']);

            Asset_record::create([
                'assets_id' => $assignment->assets_id,
                'users_id' => $assignment->users_id,
                'status' => 'assignment_updated',
            ]);
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

            Asset_record::create([
                'assets_id' => $assignment->assets_id,
                'users_id' => $assignment->users_id,
                'status' => 'assignment_deleted',
            ]);


            $assignment->delete(); 
            

            return response()->json(['success' => true, 'message' => 'Deleted and Asset released']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    

   
    
}