<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
     public static function checkPermission($permission)
    {
        if (!auth()->user()->hasPermissionTo($permission)) {
            abort(response()->json([
                'success' => false,
                'message' => 'You do not have permission to perform this action.',
            ], 403));
        }
    }
}
