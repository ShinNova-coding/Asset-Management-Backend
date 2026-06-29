<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\Assignment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class WarrantyExpiryService
{
    /**
     * Process assets with expired warranty, retire them and update assignments.
     *
     * @return array List of retired assets with refreshed data.
     */
    public function handle(): array
    {
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
                $retiredAssets[] = $asset->refresh();
            }
        });

        return $retiredAssets;
    }
}
