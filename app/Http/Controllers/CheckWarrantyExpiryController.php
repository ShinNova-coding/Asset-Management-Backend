<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;

class CheckWarrantyExpiryController extends Controller
{
    public function checkWarrantyExpiry($id)
    {
        $currentDate = now();

        $assets = Asset::where('asset_id', $id)
            ->whereRaw('DATE_ADD(purchase_date, INTERVAL warranty_period MONTH) <= ?', [$currentDate])
            ->where('status', '!=', 'disposed')
            ->get();

            if($assets){
                    $assets->update(['status'=>'retired']);
                }
                return response()->json([
                    'success' => true,
                    'message' => 'The assignment has been retired due to warranty expiry'
                ]);
            }
       
    }

