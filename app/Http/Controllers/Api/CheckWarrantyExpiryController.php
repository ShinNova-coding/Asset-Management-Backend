<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Assignment;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class CheckWarrantyExpiryController extends Controller
{
    public function checkExpiry()
    {
        try {
            $retiredAssets = [];

            $expiredAssets = Asset::where('status', '!=', 'retired')
                ->whereNotNull('purchased_date')
                ->whereNotNull('warranty_period')
                ->get()
                ->filter(function ($asset) {
                    return Carbon::parse($asset->purchased_date)
                        ->addMonths($asset->warranty_period)
                        ->startOfDay()
                        ->lte(now()->startOfDay());
                });

            DB::transaction(function () use ($expiredAssets, &$retiredAssets) {
                foreach ($expiredAssets as $asset) {
                    Assignment::where('assets_id', $asset->id)
                        ->where('status', '!=', 'returned')
                        ->update([
                            'status' => 'returned',
                            'returned_date' => now(),
                        ]);

                    $asset->update(['status' => 'retired']);

                    $retiredAssets[] = $asset->fresh();
                }
            });

            return response()->json([
                'success' => true,
                'data' => $retiredAssets,
                'message' => count($retiredAssets) > 0
                    ? 'Expired assets retired and assignments returned successfully'
                    : 'No expired assets found',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
