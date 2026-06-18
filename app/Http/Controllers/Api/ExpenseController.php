<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Exception;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        PermissionController::checkPermission('view-expenses');

        $expense = Expense::with(['user', 'maintenance', 'asset'])->latest()->get();

        if($expense->isEmpty()){
            return response()->json([
                'success' => false, 
                'message' => 'No expenses found'], 404);
        }
        return response()->json([
            'success' => true, 
            'data' => $expense,
            'message' => 'expenses retrieved successfully'], 200);
    }

    public function store(Request $request)
    {
        PermissionController::checkPermission('create-expenses');
        try{
        $request->validate([
            'users_id' => 'required|exists:users,id',
            'maintenances_id' => 'nullable|exists:maintenances,id',
            'assets_id' => 'nullable|exists:assets,id',
            'cost' => 'required|numeric',
            'expense_date' => 'required|date',
            'title' => 'required|string',
            'expense_type' => 'required|string',
            'status' => 'required|string',
            'voucher' => 'required|string',
        ]);

        $expense = Expense::create([
            'users_id' => $request->users_id,
            'maintenances_id' => $request->maintenances_id,
            'assets_id' => $request->assets_id,
            'cost' => $request->cost,
            'expense_date' => $request->expense_date,
            'title' => $request->title,
            'expense_type' => $request->expense_type,
            'status' => $request->status,
        ]);

        if ($request->has('voucher') && $request->filled('voucher')) {
                    $expense->addMediaFromBase64($request->voucher)
                        ->toMediaCollection('vouchers');
                }
                
            $image_url = $expense->getFirstMediaUrl('images') ?: null;
            $preview_url = $expense->getFirstMediaUrl('images', 'preview') ?: null;

            $expense->image_url = $image_url;
            $expense->preview_url = $preview_url;

        return response()->json([
            'success' => true, 
            'data' => $expense,
            'message' => 'expense created successfully'], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()], 500);
        }
    }

    public function show(Request $request){
        PermissionController::checkPermission('view-expenses');
        try{
        $id = $request->input('expense_id');
        $expense = Expense::with(['user', 'maintenance', 'asset'])->find($id);
        if(!$expense){
            return response()->json([
                'success' => false, 
                'message' => 'No expense found for this specific ID'], 404);
        }
            $image_url = $expense->getFirstMediaUrl('images') ?: null;
            $preview_url = $expense->getFirstMediaUrl('images', 'preview') ?: null;

            $expense->image_url = $image_url;
            $expense->preview_url = $preview_url;

        return response()->json([
            'success' => true, 
            'data' => $expense,
            'message' => 'expense retrieved successfully'], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()], 500);
        }

    }

    public function update(Request $request){
        PermissionController::checkPermission('update-expenses');
        try{
        $id = $request->input('expense_id');
        $expense = Expense::findOrFail($id);
        $request->validate([
            'users_id' => 'required|exists:users,id',
            'maintenances_id' => 'nullable|exists:maintenances,id',
            'assets_id' => 'nullable|exists:assets,id',
            'cost' => 'required|numeric',
            'expense_date' => 'required|date',
            'title' => 'required|string',
            'expense_type' => 'required|string',
            'status' => 'required|string',
            'voucher' => 'required|string',
        ]);

        $expense->update([
            'users_id' => $request->users_id,
            'maintenances_id' => $request->maintenances_id,
            'assets_id' => $request->assets_id,
            'cost' => $request->cost,
            'expense_date' => $request->expense_date,
            'title' => $request->title,
            'expense_type' => $request->expense_type,
            'status' => $request->status,
        ]);

        if ($request->has('voucher') && $request->filled('voucher')) {
                $expense->clearMediaCollection('vouchers');
                $expense->addMediaFromBase64($request->voucher)
                    ->toMediaCollection('vouchers');
            }

            $image_url = $expense->getFirstMediaUrl('images') ?: null;
            $preview_url = $expense->getFirstMediaUrl('images', 'preview') ?: null;

            $expense->image_url = $image_url;
            $expense->preview_url = $preview_url;

        return response()->json([
            'success' => true, 
            'data' => $expense,
            'message' => 'expense updated successfully'], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request){

        PermissionController::checkPermission('delete-expenses');
        try{
        $id = $request->input('expense_id');
        $expense = Expense::findOrFail($id);
        $expense->delete();
        return response()->json([
            'success' => true, 
            'message' => 'expense deleted successfully'], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()], 500);
        }

    }
}
