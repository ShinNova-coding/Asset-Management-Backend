<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    
    public function login(Request $request){
        try{
        $credential=$request->validate([
            'email'=>'required|email',
            'password'=>'required'
        ]);

        $user=User::where('email',$credential['email'])->first();
        if(!$user||!Hash::check($credential['password'],$user->password)){
            return response()->json([
                'success'=>false,
                'message'=>'Invalid email or password'
            ],401);
        }

        if($user->status=='suspended'||$user->status=='resigned'){
            return response()->json([
                'success'=>false,
                'message'=>'Your account is not active right now . Please contact admin.'
            ],401);
        }
        $token=$user->createToken('auth_token')->plainTextToken;
        $user->load('roles');
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
