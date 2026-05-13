<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    
    public function login(Request $request){
        try{
        $credential=$request->validate([
            'email'=>'required|email',
            'password'=>'required'
        ]);

        if(!Auth::attempt($credential)){
            return response()->json([
                'success'=>false,
                'message'=>'Invalid credential'
            ],404);
        }

        $user=Auth::user();
        $token=$user->createToken('auth_token')->plainTextToken;
         return response()->json([
                'success'=>true,
                'message'=>'Login Successful',
                'token'=>$token,
                'user'=>$user
            ],200);

        }catch(Exception $e){
            return response()->json([
                'success'=>false,
                'message'=>$e->getMessage()
            ]);
        }
    }

    public function logout(Request $request){
        try{
        $request->user()->currentAccessToken()->delete();
      
            return response()->json([
                'success'=>true,
                'messsage'=>'Logout Successfully'
            ]);
        }catch(Exception $e){
        return response()->json([
            'success'=>false,
            'message'=>$e->getMessage()
        ]);

        }

    }
}
