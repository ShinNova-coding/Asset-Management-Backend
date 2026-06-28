<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\PermissionController;
use Exception;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        PermissionController::checkPermission('view-roles');
        
        $roles = Role::with('permissions')->get();

        return response()->json([
            'status' => 'success',
            'data' => $roles,
            'message' => 'Roles retrieved successfully'
        ]);
    }

    public function store(Request $request)
    {
        PermissionController::checkPermission('create-roles');  
        
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'required|array'
        ]);

        $role = Role::create(['name' => $request->name]);
        $role->syncPermissions($request->permissions);

        return response()->json([
            'status' => 'success',
            'data' => $role->load('permissions'),
            'message' => 'Role created successfully'
        ], 201);
    }

    public function show(Request $request)
    {
        PermissionController::checkPermission('view-roles');
        
        $role = Role::with('permissions')->find($request->input('role_id'));

        if (!$role) {
            return response()->json(['status' => 'error', 'message' => 'Role not found'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $role,
            'message' => 'Role retrieved successfully'
        ]);
    }

    public function update(Request $request)
    {
        PermissionController::checkPermission('update-roles');
        
        $role = Role::find($request->input('role_id'));

        if (!$role) {
            return response()->json(['status' => 'error', 'message' => 'Role not found'], 404);
        }

        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
            'permissions' => 'required|array'
        ]);

        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions);

        return response()->json([
            'status' => 'success',
            'data' => $role->load('permissions'),
            'message' => 'Role updated successfully'
        ]);
    }

    public function destroy(Request $request)
    {
        PermissionController::checkPermission('delete-roles');
        $role_id=$request->input('role_id');
        $role = Role::find($role_id);

        if (!$role) {
            return response()->json(['status' => 'error', 'message' => 'Role not found'], 404);
        }

        if($role->name=='super-admin'){
            return response()->json([
                'success' => false, 
                'message' => 'Super admin role cannot be deleted'], 422);
        }

        $role->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Role deleted successfully'
        ]);
    }

    public function getAllPermissions(){

    $permissions=Permission::all();
    return response()->json([
        'status' => 'success',
        'data' => $permissions,
        'message' => 'Permissions retrieved successfully'
    ]);
    }
}