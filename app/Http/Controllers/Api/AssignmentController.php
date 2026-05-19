<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Exception;

class AssignmentController extends Controller
{
    public function index()
    {
        $assignments = Assignment::with(['asset', 'user'])->latest()->get();
        if ($assignments->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No assignments found'], 404);
        }

        }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'asset_id' => 'required|exists:assets,asset_id',
                'employee_id' => 'required|exists:users,employee_id',
                'assign_date' => 'required|date',
            ]);

            $asset = Asset::where('asset_id', $request->asset_id)->first();
            if ($asset->status !== 'available') {
                return response()->json(['success' => false, 'message' => 'Asset not available'], 400);
            }

            $assignment = Assignment::create([
                'asset_id' => $request->asset_id,
                'employee_id' => $request->employee_id,
                'assign_date' => $request->assign_date,
                'status' => 'active'
            ]);

            $asset->update(['status' => 'assigned']);

            return response()->json(['success' => true, 'data' => $assignment], 201);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $assignment = Assignment::with(['asset', 'user'])->find($id);
            if (!$assignment) {
                return response()->json(['success' => false, 'message' => 'Assignment not found'], 404);
            }
            return response()->json(['success' => true, 'data' => $assignment], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $assignment = Assignment::find($id);
            if (!$assignment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Assignment not found'
                ], 404);
            }
            $request->validate([
                'employee_id' => 'exists:users,employee_id',
                'asset_id' => 'exists:assets,asset_id',
                'assign_date' => 'date'
            ]);
            $assignment->update($request->only(['employee_id', 'asset_id', 'assign_date']));
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

    public function destroy($id)
    {
        try {
            $assignment = Assignment::findOrFail($id);

            if ($assignment->status === 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete active assignment. Please release the asset first.'
                ], 400);
            }
            Asset::where('asset_id', $assignment->asset_id)->update(['status' => 'available']);
            $assignment->delete();
            return response()->json(['success' => true, 'message' => 'Deleted and Asset released']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function release($id)
    {
        try {
            $assignment = Assignment::findOrFail($id);

            if ($assignment->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Assignment is not active'
                ], 400);
            }

            $assignment->update([
                'status' => 'released',
                'return_date' => now()
            ]);

            Asset::where('asset_id', $assignment->asset_id)->update(['status' => 'available']);

            return response()->json([
                'success' => true,
                'message' => 'Asset released successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    
}