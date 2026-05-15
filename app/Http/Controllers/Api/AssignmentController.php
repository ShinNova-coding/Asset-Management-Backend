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
        return response()->json(['success' => true, 'data' => $assignments]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,asset_id',
            'employee_id' => 'required|exists:users,employee_id',
            'assigned_date' => 'required|date',
        ]);

        try {
            $asset = Asset::where('asset_id', $request->asset_id)->first();
            if ($asset->status !== 'available') {
                return response()->json(['success' => false, 'message' => 'Asset not available'], 400);
            }

            $assignment = DB::transaction(function () use ($request, $asset) {
                $asset->update(['status' => 'assigned']);
                return Assignment::create([
                    'asset_id' => $request->asset_id,
                    'employee_id' => $request->employee_id,
                    'assigned_date' => $request->assigned_date,
                    'status' => 'active'
                ]);
            });

            return response()->json(['success' => true, 'data' => $assignment], 201);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $assignment = Assignment::with(['asset', 'user'])->find($id);
        if (!$assignment) return response()->json(['success' => false, 'message' => 'Not found'], 404);
        return response()->json(['success' => true, 'data' => $assignment]);
    }

    public function update(Request $request, $id)
    {
        $assignment = Assignment::findOrFail($id);
        $assignment->update($request->only(['returned_date', 'status']));
        
        if ($request->status === 'returned' || $request->returned_date) {
            Asset::where('asset_id', $assignment->asset_id)->update(['status' => 'available']);
        }

        return response()->json(['success' => true, 'data' => $assignment]);
    }

    public function destroy($id)
    {
        try {
            $assignment = Assignment::findOrFail($id);
            DB::transaction(function () use ($assignment) {
                Asset::where('asset_id', $assignment->asset_id)->update(['status' => 'available']);
                $assignment->delete();
            });
            return response()->json(['success' => true, 'message' => 'Deleted and Asset released']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}