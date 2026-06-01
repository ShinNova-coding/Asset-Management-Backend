<?php

use App\Http\Controllers\Admin\AssetRequestController;
use App\Http\Controllers\Admin\MaintenanceRequestController;
use App\Http\Controllers\Api\ActivitylogsController;
use App\Http\Controllers\Api\AssignmentActivitylogsController;
use App\Http\Controllers\Api\AssignmentController;
use App\Http\Controllers\Api\AssignmentHistoryController;
use App\Http\Controllers\Api\CategoryAssetController;
use App\Http\Controllers\Api\ChangePasswordController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\GetEmployeeAssignmentController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\MaintenanceActivitylogsController;
use App\Http\Controllers\Api\MaintenanceController;
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
Route::post('/login', [LoginController::class, 'login'])->name('login');

Route::post('/forgot-password', [PasswordResetController::class, 'submitForgetPasswordForm'])->name('password.email');
Route::post('/reset-password', [PasswordResetController::class, 'submitResetPasswordForm'])->name('password.update');

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::post('/change-password', [ChangePasswordController::class, 'changePassword'])->name('password.change');
  
    Route::resource('asset', AssetController::class);
    Route::get('asset/trashed', [AssetController::class, 'onlyTrashed']);
    Route::post('asset/{id}/restore', [AssetController::class, 'restore']);   

    Route::post('/asset/{id}/request', [AssetRequestController::class, 'assignRequest']);
    Route::post('/admin/requests/{id}/approve', [AssetRequestController::class, 'approve'])->name('assignment.approve');
    Route::post('/admin/requests/{id}/cancel', [AssetRequestController::class, 'cancel'])->name('assignment.cancel');

    Route::resource('assignment', AssignmentController::class);
    Route::get('/assignment/history',[AssignmentHistoryController::class,'assignmentHistory'])->name('assignment.history');
    Route::get('/assignment/{id}/asset',[GetEmployeeAssignmentController::class,'getEmployeeAsset'])->name('assignment.getEmployeeAsset');
    Route::post('/assignment/{id}/return', [ReturnAssignmentController::class, 'returnAssignment'])->name('assignment.return');

    Route::resource('maintenance', MaintenanceController::class);
    Route::post('/maintenance/{id}/request', [MaintenanceRequestController::class, 'maintainRequest'])->name('maintenance.request');
    Route::post('/admin/requests/maintenance/{id}/approve', [MaintenanceRequestController::class, 'approve'])->name('maintenance.approve');
    Route::post('/admin/requests/maintenance/{id}/maintain', [MaintenanceRequestController::class, 'maintain'])->name('maintenance.maintain');  
    Route::post('/admin/requests/maintenance/{id}/complete', [MaintenanceRequestController::class, 'complete'])->name('maintenance.complete');
    Route::post('/admin/requests/maintenance/{id}/cancel', [MaintenanceRequestController::class, 'cancel'])->name('maintenance.cancel');

    Route::get('/report/user', [ReportController::class, 'userReport'])->name('report.user');
    Route::get('/report/asset', [ReportController::class, 'assetReport'])->name('report.asset');
    Route::get('/report/category', [ReportController::class, 'categoryReport'])->name('report.category');

    Route::get('/dashboard',[DashboardController::class,'dashboardview'])->name('dashboard');

    Route::post('/admin/users/{id}/suspended', [UserSuspendResignController::class, 'suspended'])->name('user.suspended');
    Route::post('/admin/users/{id}/resigned', [UserSuspendResignController::class, 'resigned'])->name('user.resigned');
    
    Route::resource('user', UserController::class);
    Route::get('/profile',[ProfileController::class,'viewProfile']);
    Route::post('/profile/edit',[ProfileController::class,'editProfile']);

    Route::resource('role', RoleController::class);

    Route::resource('category', CategoryController::class);
    Route::get('/category/{id}/assets',[CategoryAssetController::class,'categoryAsset']);

    Route::get('/activitylogs',[ActivitylogsController::class,'activitylogs']);
    Route::get('/assignmentlogs',[AssignmentActivitylogsController::class,'assignmentActivitylogs']);
    Route::get('/maintenancelogs',[MaintenanceActivitylogsController::class,'maintenanceActivitylogs']);

});


