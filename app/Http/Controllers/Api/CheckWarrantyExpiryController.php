<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CheckWarrantyExpiryController extends Controller
{
    public function checkExpiry($expiry){
    $currentDate=now()->toDateString();
    $asset=Asset::where('status','!=','retired')->get();

    $expiryasset=$asset->filter(function($asset){
        $expiryDate=Carbon::parse($asset->purchased_date)->addMonths($asset->warrenty_period);

        $expiryDate->isPast();
    });
    return response()->json([
        'success'=>true,
        'data'=>$expiryasset,
        'message'=>'Expiry Asset retrieved successfully'
    ]);
    }
}
