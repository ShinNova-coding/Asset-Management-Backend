<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class ExpenseRequestController extends Controller
{
    public function updateStatus(Request $request)
    {
        $status=$request->input('status');
        

        switch($status){
            case 'requested':
                PermissionController::checkPermission('create-expenses');

                $request->validate([
            'title'=>'required',
            'expense_type'=>'required',
            'description'=>'required',
            'cost'=>'required',
            'expense_date'=>'required|date',
            'voucher'=>'required|string'
        ]);

        $expense=Expense::create([
            'users_id'=>auth()->user()->id,
            'title'=>$request->title,
            'expense_type'=>$request->expense_type,
            'description'=>$request->description,
            'cost'=>$request->cost,
            'expense_date'=>$request->expense_date,
            'status'=>'requested'
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
                'success'=>true,
                'data'=>$expense,
                'message'=>'Expense requested successfully'
            ]);

            case 'approved':
                PermissionController::checkPermission('approve-expense-requests');
                $id=$request->input('expense_id');
                
                $expense=Expense::find($id);
                if(!$expense){
                    return response()->json([
                        'success'=>false,
                        'message'=>'User not found'
                    ]);
                }
                
                $request->validate([
                    'remark'=>'required'
                ]);

                $expense->update([
                    'remark'=>$request->remark,
                    'status'=>'approved',
                    'approved_by'=>$request->user()->id
                ]);

                return response()->json([
                    'success'=>true,
                    'data'=>$expense,
                    'message'=>'Expense approved successfully'
                ]);

                case 'canceled':
                PermissionController::checkPermission('cancel-expense-requests');
                $id=$request->input('expense_id');
                
                $expense=Expense::find($id);
                if(!$expense){
                    return response()->json([
                        'success'=>false,
                        'message'=>'User not found'
                    ]);
                }
                $expense=Expense::find($id);

                $expense->update([
                    'status'=>'canceled',
                ]);

                return response()->json([
                    'success'=>true,
                    'data'=>$expense,
                    'message'=>'Expense cancelled successfully'
                ]);
            
                
        }

        
    }
}
