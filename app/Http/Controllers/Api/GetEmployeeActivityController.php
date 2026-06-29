<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Assignment;
use App\Models\Expense;
use App\Models\Maintenance;
use Exception;
use Illuminate\Http\Request;

class GetEmployeeActivityController extends Controller
{
    public function getemployeemaintenance(Request $request)
    {
        PermissionController::checkPermission('view-maintenances');
        try {
            $id = $request->user()->id;

            $maintenance = Maintenance::with(['asset.category', 'asset.media'])
                ->where('users_id', $id)
                ->get();
            if ($maintenance->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active maintenance found for the employee'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $maintenance,
                'Message' => 'Asset retrieved according to employee maintenance'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);

        }
    }


    public function getEmployeeAsset(Request $request)
    {

        PermissionController::checkPermission('view-assignments');
        try {
            $id = $request->user()->id;
            $assignment = Assignment::with('asset.category', 'asset.media')
                ->where('users_id', $id)
                ->where('status', 'active')
                ->whereHas('asset')
                ->get()
                ->pluck('asset')
                ->map(function ($asset) {
                    if ($asset) {
                        $asset->image_url = $asset->getFirstMediaUrl('images') ?: null;

                        unset($asset->media);
                    }
                    return $asset;
                });


            if ($assignment->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active assignments found for the employee'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $assignment,
                'Message' => 'Asset retrieved according to employee assign'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getEmployeeExpense(Request $request)
    {
        PermissionController::checkPermission('create-expenses');
        try {
            $id = $request->user()->id;
            $expense = Expense::with('user', 'asset', 'maintenance')
                ->where('users_id', $id)
                ->get();

            if ($expense->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No expenses found for the employee'
                ], 404);
            }
            return response()->json([
                'success' => true,
                'data' => $expense,
                'Message' => 'Expense retrieved according to employee'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
