<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Assignment;
use App\Models\User;
use App\Notifications\AssetAssignmentNotification;
use Illuminate\Http\Request;

class AssetAssignmentController extends Controller
{
    public static function suspended(string $id){

    PermissionController::checkPermission('update-users');
    $user=User::find($id);

    $user->update(['status'=>'suspended']);

    $assignments=Assignment::where('employee_id',$id)
                            ->where('status','active')
                            ->get();
    
    foreach($assignments as $assignment){
        $assignment->update(['status'=>'returned']);
    }

        $assets=Asset::whereIn('id', $assignments->pluck('asset_id'))->get();
    
    foreach($assets as $asset){
            $asset->update(['status'=>'available']);
        }

        return response()->json([
            'success' => true,
            'message' => 'User suspended and active assignments returned successfully'
        ], 200);
    }

    public static function resigned(string $id){

        PermissionController::checkPermission('update-users');
        $user=User::find($id);

        $user->update(['status'=>'resigned']);

        $assignments=Assignment::where('employee_id',$id)
                                ->where('status','active')
                                ->get();

        $assets=Asset::whereIn('id', $assignments->pluck('asset_id'))->get();

        

        foreach($assignments as $assignment){
            $assignment->update(['status'=>'returned']);
        }

    
        foreach($assets as $asset){
            $asset->update(['status'=>'available']);
        }        
        
        return response()->json([
            'success' => true,
            'message' => 'User inactivated and active assignments returned successfully'
        ], 200);
    }

    

}
