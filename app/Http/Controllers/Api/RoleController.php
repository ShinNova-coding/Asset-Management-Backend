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
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        PermissionController::checkPermission('view-roles');
        $roles= Role::with('permissions')->get();
        if(!$roles){
            return response()->json([
                'status' => 'error',
                'message' => 'No roles found'
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'data' => $roles,
            'message' => 'Roles retrieved successfully'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        PermissionController::checkPermission('create-roles');  
        try{
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions'=>'required|array'
        ]);

        $role = Role::create(['name' => $request->name]);

        $role->hasPermissionTo($request->permissions);
        return response()->json([
            'status' => 'success',
            'data' => $role,
            'message' => 'Role created successfully'
        ], 201);}
        catch(Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create role: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        PermissionController::checkPermission('view-roles');
        try{
         /** @var \Spatie\Permission\Models\Role|null $role */
        $role = Role::find($id);
        if (! $role) {
            return response()->json([
                'status' => 'error',
                'message' => 'Role not found'
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'data' => $role->load('permissions'),
            'message' => 'Role retrieved successfully'
        ]); }
        catch(Exception $e){
            return response()->json([
                'status' => 'error',
                'message' =>  $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    




    public function update(Request $request, string $id)
    {
        PermissionController::checkPermission('update-roles');
        try {
            /** @var \Spatie\Permission\Models\Role|null $role */
            $role = Role::find($id);

            if (!$role) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Role not found'
                ], 404);
            }

            $request->validate([
                'name' => 'required|unique:roles,name,' . $role->id,
            ]);

            $role->update([
                'name' => $request->name,
            ]);

            return response()->json([
                'status' => 'success',
                'data' => $role,
                'message' => 'Role updated successfully'
            ]);

        } catch (Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(string $id)
    {
        PermissionController::checkPermission('delete-roles');
        try {
            /** @var \Spatie\Permission\Models\Role|null $role */
            $role = Role::find($id);

            if (!$role) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Role not found'
                ], 404);
            }

            $role->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Role deleted successfully'
            ]);

        } catch (Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
}

