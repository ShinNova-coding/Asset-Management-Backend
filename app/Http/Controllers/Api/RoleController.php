<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;


class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $role= Role::all();
        return response()->json([
            'status' => 'success',
            'data' => $role,
            'message' => 'Role retrieved successfully'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try{
        $request->validate([
            'name' => 'required|unique:roles,name',
        ]);

        $role = Role::create(['name' => $request->name]);

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
        try{
        $role = Role::find($id);
        if (! $role) {
            return response()->json([
                'status' => 'error',
                'message' => 'Role not found'
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'data' => $role
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

