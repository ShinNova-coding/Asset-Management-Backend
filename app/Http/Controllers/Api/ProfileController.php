<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{

    public function viewProfile(Request $request){


    $user=$request->user();

       $image_url = $user->getFirstMediaUrl('images') ?: null;
            $preview_url = $user->getFirstMediaUrl('images', 'preview') ?: null;

            $user->image_url = $image_url;
            $user->preview_url = $preview_url;
        return response()->json([
            'success'=>true,
            'data'=>$user,
            'Message'=>'profile Data retrieve successfully'
        ]);
    }

    public function editProfile(Request $request){
        $user=$request->user();

        $request->validate([
            'image'=>'required|string'
        ]);

        if ($request->has('image') && $request->filled('image')) {
                $user->clearMediaCollection('images');
                $user->addMediaFromBase64($request->image)
                    ->toMediaCollection('images');
            }

         $image_url = $user->getFirstMediaUrl('images') ?: null;
            $preview_url = $user->getFirstMediaUrl('images', 'preview') ?: null;

            $user->image_url = $image_url;
            $user->preview_url = $preview_url;

        return response()->json([
            'success'=>true,
            'data'=>$user,
            'message'=>'User image updated successfully'
        ]);
    }
}
