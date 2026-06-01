<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\PermissionController;
use App\Models\Asset;
use App\Models\Assignment;
use App\Models\User;
use Illuminate\Http\Request;

class UserSuspendResignController extends Controller
{
    public static function suspended(string $id)
    {

        PermissionController::checkPermission('update-users');
        $user = User::where('employee_id', $id)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No user found.'
            ], 404);
        }
        $user->update(['status' => 'suspended']);

        $assignments = Assignment::where('employee_id', $id)
            ->where('status', 'active')
            ->get();

        foreach ($assignments as $assignment) {
            $assignment->update(['status' => 'returned']);
        }

        $assets = Asset::whereIn('id', $assignments->pluck('asset_id'))->get();

        foreach ($assets as $asset) {
            $asset->update(['status' => 'available']);
        }

        return response()->json([
            'success' => true,
            'message' => 'User suspended and active assignments returned successfully'
        ], 200);
    }

    public static function resigned(string $id)
    {

        PermissionController::checkPermission('update-users');
        $user = User::where('employee_id', $id)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No user found.'
            ], 404);
        }
        $user->update(['status' => 'resigned']);

        $assignments = Assignment::where('employee_id', $id)
            ->where('status', 'active')
            ->get();

        $assets = Asset::whereIn('id', $assignments->pluck('asset_id'))->get();



        foreach ($assignments as $assignment) {
            $assignment->update(['status' => 'returned']);
        }


        foreach ($assets as $asset) {
            $asset->update(['status' => 'available']);
        }

        return response()->json([
            'success' => true,
            'message' => 'User inactivated and active assignments returned successfully'
        ], 200);
    }


}
