<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Assignment;
use Exception;
use Illuminate\Http\Request;

class AssignController extends Controller
{
    public function availableAssets(Request $request)
    {
        try{
       $assets_id=$request->input('assets_id');

       if(!$assets_id){
        return response()->json([
            'success' => false, 
            'message' => 'Asset ID is invalid'
            ], 400);
       }
       $asset=Asset::where('id', $assets_id)->first();

       if(!$asset){
        return response()->json([
            'success' => false, 
            'message' => 'Asset not found'
            ], 404);
       }

      $assignment= Assignment::where('assets_id', $assets_id)->first() ;

      if(!$assignment){
        return response()->json([
            'success' => false, 
            'message' => 'Asset is not assigned'
            ], 404);
       }

       $assignment->update(['status' => 'returned']);
       
       $asset->update(['status' => 'available']);

       return response()->json([
        'success' => true, 
        'message' => 'Asset available for reassignment successfully'
        ], 200);

        }catch(Exception $e){
            return response()->json([
                'success' => false, 
                'message' => 'Failed to release asset: ' . $e->getMessage()
                ], 500);
        }
    }

    public function reassignAsset(Request $request)
    {
        try{
            $asset_id=$request->input('asset_id');
            $users_id=$request->input('users_id');
      $assignment= Assignment::where('assets_id', $asset_id)->first();
        if(!$assignment){
            return response()->json([
                'success' => false, 
                'message' => 'Asset is not assigned'
                ], 404);
        }

        $assignment->update(['users_id' => $users_id, 'status' => 'active']);

        $asset=Asset::where('id', $asset_id)->first();

        $asset->update(['status' => 'assigned']);

        return response()->json([
            'success' => true, 
            'data' => $assignment,
            'message' => 'Asset reassigned successfully'
            ], 200);
        }catch(Exception $e){
            return response()->json([
                'success' => false, 
                'message' => 'Failed to reassign asset: ' . $e->getMessage()
                ], 500);
        }
    }


}
