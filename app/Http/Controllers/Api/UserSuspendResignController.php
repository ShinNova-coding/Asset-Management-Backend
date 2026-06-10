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
    public static function updateStatus(Request $request)
    {

        PermissionController::checkPermission('update-users');
        $id = $request->input('id');
        $status = $request->input('status');
        $user = User::where('id', $id)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No user found.'
            ], 404);
        }
        $user->update(['status' => $status]);

        $assignments = Assignment::where('users_id', $id)
            ->where('status', 'active')
            ->get();

        foreach ($assignments as $assignment) {
            $assignment->update(['status' => 'returned']);
        }

        $assets = Asset::whereIn('id', $assignments->pluck('assets_id'))->get();

        foreach ($assets as $asset) {
            $asset->update(['status' => 'available']);
        }

        return response()->json([
            'success' => true,
            'message' => 'User status updated and active assignments returned successfully'
        ], 200);
    }

}
