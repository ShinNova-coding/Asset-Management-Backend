<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = User::with('roles')->get();
        if ($user->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'no user found',
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $user,
            'message' => 'User found successfully!!',
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'employee_id' => 'required|string|unique:users,employee_id',
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'joined_date' => 'required|date',
                'password' => 'required|min:8|confirmed',
                'role'=>'required'
            ]);

            $user=User::create([
                'employee_id' => $request->employee_id,
                'name' => $request->name,
                'email' => $request->email,
                'joined_date' => $request->joined_date,
                'password' => Hash::make($request->password),
                'status' => 'active',
            ]);

            $user->assignRole($request->role);

            $user->load('roles');
            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => 'User created successfully!!',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $showuser = User::firstWhere('employee_id', $id);
        if (! $showuser) {

            return response()->json([
                'success' => false,
                'message' => 'No user found for this specific ID',
            ]);
        }
        $showuser->load('roles');

        return response()->json([
            'success' => true,
            'data' => $showuser,
            'message' => 'User found successfully!!',
        ], 200);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $user = User::firstWhere('employee_id', $id);
            $request->validate([
                'name' => 'required|string|max:255',
                'role' => 'required',
                'email' => 'required|email|unique:users,email,'.$user->employee_id.',employee_id',
                'left_date' => 'nullable|date|after_or_equal:joined_date',
            ]);

            $data = $request->except('password','role');

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }
            $user->update($data);

            if($request->filled('role')){
                $user->syncRoles($request->role);
            }

            $user->load('roles');
            return response()->json([
                'success' => true,
                'data' => $user->refresh(),
                'message' => 'User updated successfully!!',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $user = User::firstWhere('employee_id', $id);
            
            if (! $user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found!!',
                ], 404);
            }

            if($user->hasRole('admin')){
                return response()->json([
                    'success' => false,
                    'message' => 'Admin user cannot be deleted!!',
                ], 403);
            }
            $user->delete();
            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully!!',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }

    }
}
