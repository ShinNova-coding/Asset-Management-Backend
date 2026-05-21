<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PermissionController;
use App\Models\Asset;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    function dashboardview(Request $request){

    PermissionController::checkPermission('view-dashboard');
    $status=[
        'total_assets' => Asset::count(),
        'available_assets' => Asset::where('status', 'available')->count(),
        'assigned_assets' => Asset::where('status', 'assigned')->count(),
        'maintenance_assets' => Asset::where('status', 'maintenance')->count(),
        'retired_assets' => Asset::where('status', 'retired')->count(),
    ];

    return response()->json([
        'status' => 'success',
        'data' => $status,
        'message' => 'Dashboard data retrieved successfully.'
    ]);
    }
}
