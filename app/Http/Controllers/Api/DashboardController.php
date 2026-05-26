<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\PermissionController;
use App\Models\Asset;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    function dashboardview(Request $request){

    PermissionController::checkPermission('view-dashboard');
    $asset=[
       'total_assets' => Asset::count(),
        'available_assets' => Asset::where('status', 'available')->count(),
        'assigned_assets' => Asset::where('status', 'assigned')->count(),
        'maintenance_assets' => Asset::where('status', 'maintenance')->count(),
        'retired_assets' => Asset::where('status', 'retired')->count(),
    ];

    $user=[
        'total_users'=>User::count(),
        'active_user'=>User::where('status','active')->count(),
        'suspended_user'=>User::where('status','suspended')->count(),
        'resigned_user'=>User::where('status','resigned')->count()
    ];

    $data=Asset::join('categories', 'assets.category_id', '=', 'categories.id')
    ->select('categories.name as category')
    ->selectRaw('COUNT(*) as total')
    ->groupBy('categories.name')
    ->get();

    $result=$data->pluck('total','category');


    return response()->json([
        'status' => 'success',
        'data' => [
            'asset'=>$asset,
            'user'=>$user,
           'category'=> $result
        ],
        'message' => 'Dashboard data retrieved successfully.'
    ]);
    }
}
