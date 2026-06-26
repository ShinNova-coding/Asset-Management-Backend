<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class PasswordResetController extends Controller
{
    public function submitForgetPasswordForm(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        if(User::where('email', $request->email)->exists()){
            return response()->json([
                'success' => true,
                'message' => 'Now You can reset your password'
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'Wrong email address!!!'
        ]);
    }
    
    public function submitResetPasswordForm(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
            
        ]); 

        if(User::where('email', $request->email)->exists()){
            $user = User::where('email', $request->email)->first();
            $user->password = bcrypt($request->password);
            $user->save();
            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully'
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'Wrong email address!!!'
        ]);
    }
}
