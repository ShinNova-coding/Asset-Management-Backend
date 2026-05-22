<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Assignment;
use App\Models\User;
use App\Notifications\AssetAssignmentNotification;
use Illuminate\Http\Request;

class AssetAssignmentController extends Controller
{
    public static function suspend(string $id){

    PermissionController::checkPermission('update-users');
    $user=User::find($id);

    $user->update(['status'=>'suspended']);

    $assignments=Assignment::where('employee_id',$id)
                            ->where('status','active')
                            ->get();

    $assets=Asset::whereIn('id', $assignments->pluck('asset_id'))->get();

    foreach($assignments as $assignment){
        $user->notify(new AssetAssignmentNotification($assignment));
        }

        $assignments->pluck('id')->update(['status'=>'returned']);

        foreach($assets as $asset){
            $asset->update(['status'=>'available']);
        }

        return response()->json([
            'success' => true,
            'message' => 'User suspended and active assignments returned successfully'
        ], 200);
    }

    public static function resign(string $id){

        PermissionController::checkPermission('update-users');
        $user=User::find($id);

        $user->update(['status'=>'resign']);

        $assignments=Assignment::where('employee_id',$id)
                                ->where('status','active')
                                ->get();

        $assets=Asset::whereIn('id', $assignments->pluck('asset_id'))->get();

        foreach($assignments as $assignment){
        $user->notify(new AssetAssignmentNotification($assignment));
        }

        $assignments->pluck('id')->update(['status'=>'returned']);

    
        foreach($assets as $asset){
            $asset->update(['status'=>'available']);
        }        return response()->json([
            'success' => true,
            'message' => 'User inactivated and active assignments returned successfully'
        ], 200);
    }

    

}
