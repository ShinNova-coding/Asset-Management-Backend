<?php

use App\Http\Controllers\Admin\ExpenseRequestController;
use App\Http\Controllers\Admin\MaintenancestatusController;
use App\Http\Controllers\Api\AssignController;
use App\Http\Controllers\Api\CategoryAssetController;
use App\Http\Controllers\Admin\AssetRequestController;
use App\Http\Controllers\Admin\MaintenanceRequestController;
use App\Http\Controllers\Api\ActivitylogsController;
use App\Http\Controllers\Api\AssignmentActivitylogsController;
use App\Http\Controllers\Api\AssignmentController;
use App\Http\Controllers\Api\AssignmentHistoryController;
use App\Http\Controllers\Api\AssignmentRequestController;
use App\Http\Controllers\Api\ChangePasswordController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\GetEmployeeAssignmentController;
use App\Http\Controllers\Api\GetEmployeeMaintenanceController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\MaintenanceActivitylogsController;
use App\Http\Controllers\Api\MaintenanceController;
use App\Http\Controllers\Api\MonthlyYearlyReportController;
use App\Http\Controllers\Api\PasswordResetController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\AssetController;
use App\Http\Controllers\Api\UserSuspendResignController;
use App\Http\Controllers\Api\ReturnAssignmentController;
use Illuminate\Support\Facades\Route;

//Login routes
Route::post('/login', [LoginController::class, 'login'])->name('login')->middleware('throttle:login');

Route::post('/forgot-password', [PasswordResetController::class, 'submitForgetPasswordForm'])->name('password.email');
Route::post('/reset-password', [PasswordResetController::class, 'submitResetPasswordForm'])->name('password.update');

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::post('/change-password', [ChangePasswordController::class, 'changePassword'])->name('password.change');

    Route::get('asset/trashed', [AssetController::class, 'onlyTrashed']);
    Route::post('asset/restore', [AssetController::class, 'restore']);
    Route::resource('asset', AssetController::class);

    Route::get('/assignment/history', [AssignmentHistoryController::class, 'assignmentHistory'])->name('assignment.history');
    Route::get('/assignment/asset', [GetEmployeeAssignmentController::class, 'getEmployeeAsset'])->name('assignment.getEmployeeAsset');
    Route::post('/assignment/return', [ReturnAssignmentController::class, 'returnAssignment'])->name('assignment.return');
    Route::resource('assignment', AssignmentController::class);

   
    Route::get('/report/user', [ReportController::class, 'userReport'])->name('report.user');
    Route::get('/report/asset', [ReportController::class, 'assetReport'])->name('report.asset');
    Route::get('/report/category', [ReportController::class, 'categoryReport'])->name('report.category');

    Route::get('/dashboard', [DashboardController::class, 'dashboardview'])->name('dashboard');

    Route::post('/admin/users/status', [UserSuspendResignController::class, 'updateStatus']);
    Route::resource('user', UserController::class);
    Route::get('/profile', [ProfileController::class, 'viewProfile']);
    Route::post('/profile/edit', [ProfileController::class, 'editProfile']);

    Route::resource('role', RoleController::class);

    Route::get('/category/assets', [CategoryAssetController::class, 'categoryAsset']);
    Route::resource('category', CategoryController::class);

    Route::get('/maintenance/asset',[GetEmployeeMaintenanceController::class, 'getEmployeeMaintenance'])->name('maintenance.getEmployeeMaintenance');
    Route::post('/maintenance/status', [MaintenanceRequestController::class, 'updateStatus'])->name('maintenance.request');
    Route::post('/admin/maintenance/status', [MaintenanceRequestController::class, 'updateStatus'])->name('maintenance.status');
    Route::post('/admin/maintenance/availableasset', [AssignController::class, 'availableAssets'])->name('asset.available');
    Route::post('/admin/maintenance/reassign', [AssignController::class, 'reassignAsset'])->name('asset.reassign');
    Route::resource('maintenance', MaintenanceController::class);

    Route::get('/activitylogs', [ActivitylogsController::class, 'activitylogs']);
    Route::get('/assignmentlogs', [AssignmentActivitylogsController::class, 'assignmentActivitylogs']);
    Route::get('/maintenancelogs', [MaintenanceActivitylogsController::class, 'maintenanceActivitylogs']);

    Route::post('/expense/status', [ExpenseRequestController::class, 'updateStatus']);
    Route::get('/expense/report', [MonthlyYearlyReportController::class, 'monthlyyearlyReport']);
    Route::resource('expense', ExpenseController::class);

});


