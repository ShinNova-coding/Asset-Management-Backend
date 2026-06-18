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
        $expenses = Expense::whereMonth('expense_date', '=', $month)
        ->whereYear('expense_date', '=', $year)
        ->sum('cost');

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
