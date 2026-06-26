<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;

class MonthlyYearlyReportController extends Controller
{
    public function monthlyyearlyReport(Request $request) {
        PermissionController::checkPermission('view-expenses');

        $month=$request->input('month');
        $year=$request->input('year');

        $query = Expense::whereYear('expense_date', '=', $year)
        ->where('status', '=', 'approved');

        if($month){
            $query->whereMonth('expense_date', '=', $month);
        }

        $expenses = $query->sum('cost');
        
        if(!$expenses){
            return response()->json([
                'success' => false, 
                'message' => 'No expenses found'], 404);
        }
        return response()->json([
            'success' => true, 
            'data' => $expenses,
            'message' => 'expenses retrieved successfully'], 200);
    }
}
