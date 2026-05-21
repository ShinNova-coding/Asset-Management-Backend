<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AssetAssignmentController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PermissionController;
use App\Models\Assignment;
use App\Models\User;
use Auth;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        PermissionController::checkPermission('view-users');
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
        PermissionController::checkPermission('create-users');
        try {
            $request->validate([
                'employee_id' => 'required|string|unique:users,employee_id',
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'joined_date' => 'required|date',
                'position' => 'nullable|string|max:255',
                'phone_number' => 'nullable|string|max:20',
                'password' => 'required|min:8|confirmed',
                'role' => 'required',
                'image' => 'required|string'
            ]);

            $user = DB::transaction(function () use ($request) {

                $user = User::create([
                    'employee_id' => $request->employee_id,
                    'name' => $request->name,
                    'email' => $request->email,
                    'joined_date' => $request->joined_date,
                    'position' => $request->position,
                    'phone_number' => $request->phone_number,
                    'password' => Hash::make($request->password),
                    'status' => 'active',
                ]);

                $user->assignRole($request->role);

                $user->load('roles');

                if ($request->has('image') && $request->filled('image')) {
                    $user->addMediaFromBase64($request->image)
                        ->toMediaCollection('images');
                }
                return $user;
            });

            $image_url = $user->getFirstMediaUrl('images') ?: null;
            $preview_url = $user->getFirstMediaUrl('images', 'preview') ?: null;

            $user->image_url = $image_url;
            $user->preview_url = $preview_url;
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
        PermissionController::checkPermission('view-users');
        $showuser = User::firstWhere('employee_id', $id);
        if (!$showuser) {

            return response()->json([
                'success' => false,
                'message' => 'No user found for this specific ID',
            ]);
        }
        $showuser->load('roles');

        $image_url = $showuser->getFirstMediaUrl('images') ?: null;
        $preview_url = $showuser->getFirstMediaUrl('images', 'preview') ?: null;

        $showuser->image_url = $image_url;
        $showuser->preview_url = $preview_url;

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
        PermissionController::checkPermission('update-users');
        try {
            $user = User::firstWhere('employee_id', $id);
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->employee_id . ',employee_id',
                'joined_date' => 'required|date',
                'position' => 'nullable|string|max:255',
                'phone_number' => 'nullable|string|max:20',
                'role' => 'required',
                'status' => 'required|in:active,inactive,suspended',
                'image' => 'required|string'
            ]);

            if($request->status ==='suspended'){
                AssetAssignmentController::suspend($user->employee_id);
            }

            if($request->status ==='inactive'){
                AssetAssignmentController::inactive($user->employee_id);
            }

            $data = $request->except('password', 'role', 'image');

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            if ($request->has('image') && $request->filled('image')) {
                $user->clearMediaCollection('images');
                $user->addMediaFromBase64($request->image)
                    ->toMediaCollection('images');
            }
            $user->update($data);

            if ($request->filled('role')) {
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
        PermissionController::checkPermission('delete-users');
        try {
            $user = User::firstWhere('employee_id', $id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found!!',
                ], 404);
            }

            if ($user->hasRole('admin')) {
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
     public function getNotification(){
        PermissionController::checkPermission('get-notifications');
    $notification=Auth::user()->unreadNotifications();
    return response()->json([
        'success'=>true,
        'message'=>'notification send successfully',
        'data'=>$notification
    ]);
    }

}
