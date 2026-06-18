<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public static function assetReport(){

    $status=[
        'total_assets' => Asset::count(),
        'available_assets' => Asset::where('status', 'available')->count(),
        'assigned_assets' => Asset::where('status', 'assigned')->count(),
        'maintenance_assets' => Asset::where('status', 'maintenance')->count(),
        'retired_assets' => Asset::where('status', 'retired')->count(),
    ];

    return response()->json([
        'success'=>true,
        'data'=>$status,
        'Message'=>'Asset report retrieved successfully'
    ]);
    }

    public static function userReport(){
        $status=[
        'total_users'=>User::count(),
        'active_user'=>User::where('status','active')->count(),
        'suspended_user'=>User::where('status','suspended')->count(),
        'resigned_user'=>User::where('status','resigned')->count()
        ];

        return response()->json([
            'success'=>true,
            'data'=>$status,
            'message'=>'User report retrieved successfully'
        ]);
    }

    public static function categoryReport(){
        $asset = Asset::join('categories', 'assets.category_id', '=', 'categories.id')
    ->select('categories.name as category')
    ->selectRaw("COUNT(CASE WHEN assets.status = 'available' THEN 1 END) as available")
    ->selectRaw("COUNT(CASE WHEN assets.status = 'pending' THEN 1 END) as pending")
    ->selectRaw("COUNT(CASE WHEN assets.status = 'assigned' THEN 1 END) as assigned")
    ->selectRaw("COUNT(CASE WHEN assets.status = 'maintenance' THEN 1 END) as maintenance")
    ->selectRaw("COUNT(CASE WHEN assets.status = 'retired' THEN 1 END) as retired")
    ->groupBy('categories.name')
    ->get();

    $data = Asset::join('categories', 'assets.category_id', '=', 'categories.id')
    ->select('categories.name as category')
    ->selectRaw('COUNT(*) as total')
    ->groupBy('categories.name')
    ->get();

    $result=$data->pluck('total','category');

        return response()->json([
            'success'=>true,
            'data'=>$result,
            'detail_data'=>$asset,
            'message'=>'Category Report retrieved successfully'
        ]);
    }
    
}
