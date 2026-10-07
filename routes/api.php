<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CompanyApplicationController;
use App\Http\Controllers\Api\CompanyJobController;
use App\Http\Controllers\Api\CompanyProfileController;
use App\Http\Controllers\Api\JobPostingController;
use App\Http\Controllers\Api\StudentApplicationController;
use App\Http\Controllers\Api\StudentProfileController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware(['auth:sanctum', 'throttle:api', 'ensure.active']);

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register'])->middleware('throttle:register');
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:login');
    Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->middleware('throttle:auth.sensitive');
    Route::post('reset-password', [AuthController::class, 'resetPassword'])->middleware('throttle:auth.sensitive');

    Route::middleware(['auth:sanctum', 'ensure.active', 'throttle:api'])->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('logout-all', [AuthController::class, 'logout-all']);
        Route::get('me', [AuthController::class, 'me']);
        Route::post('change-password', [AuthController::class, 'changePassword']);
        Route::post('email/resend', [AuthController::class, 'resendVerificationEmail'])->middleware('throttle:auth.sensitive');
    });
});

Route::get('/auth/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return response()->json([
        'success' => true,
        'message' => 'Email verified successfully.',
    ]);
})->middleware(['auth:sanctum', 'signed'])->name('verification.verify');

Route::get('jobs', [JobPostingController::class, 'index'])->middleware('throttle:api');
Route::get('jobs/{id}', [JobPostingController::class, 'show'])->middleware('throttle:api');

Route::middleware(['auth:sanctum', 'ensure.active', 'ensure.verified', 'role:student', 'throttle:api'])->prefix('student')->group(function () {
    Route::get('profile', [StudentProfileController::class, 'show']);
    Route::post('profile', [StudentProfileController::class, 'store']);
    Route::put('profile', [StudentProfileController::class, 'update']);
    Route::delete('profile', [StudentProfileController::class, 'destroy']);
    Route::post('profile/cv', [StudentProfileController::class, 'uploadCv']);
    Route::get('profile/cv', [StudentProfileController::class, 'downloadCv']);
    Route::delete('profile/cv', [StudentProfileController::class, 'deleteCv']);

    Route::get('applications/daily-limit', [ApplicationController::class, 'dailyLimit']);
    Route::get('applications', [StudentApplicationController::class, 'index']);
    Route::get('applications/{id}', [StudentApplicationController::class, 'show']);
    Route::delete('applications/{id}', [StudentApplicationController::class, 'destroy']);
});

Route::middleware(['auth:sanctum', 'ensure.active', 'ensure.verified', 'role:student', 'throttle:api'])->group(function () {
    Route::post('jobs/{job}/apply', [ApplicationController::class, 'apply']);
});

Route::middleware(['auth:sanctum', 'ensure.active', 'ensure.verified', 'role:company', 'throttle:api'])->prefix('company')->group(function () {
    Route::get('profile', [CompanyProfileController::class, 'show']);
    Route::post('profile', [CompanyProfileController::class, 'store']);
    Route::put('profile', [CompanyProfileController::class, 'update']);
    Route::delete('profile', [CompanyProfileController::class, 'destroy']);

    Route::post('jobs', [CompanyJobController::class, 'store']);
    Route::get('jobs', [CompanyJobController::class, 'index']);
    Route::get('jobs/{id}', [CompanyJobController::class, 'show']);
    Route::put('jobs/{id}', [CompanyJobController::class, 'update']);
    Route::delete('jobs/{id}', [CompanyJobController::class, 'destroy']);
    Route::post('jobs/{id}/publish', [CompanyJobController::class, 'publish']);
    Route::post('jobs/{id}/close', [CompanyJobController::class, 'close']);

    Route::get('jobs/{job}/applications', [CompanyApplicationController::class, 'index']);
    Route::get('applications/{id}', [CompanyApplicationController::class, 'show']);
    Route::patch('applications/{id}/status', [CompanyApplicationController::class, 'updateStatus']);
});

Route::middleware(['auth:sanctum', 'ensure.active', 'ensure.verified', 'role:admin', 'throttle:api'])->prefix('admin')->group(function () {
    Route::get('dashboard', [AdminController::class, 'dashboard']);
    Route::get('users', [AdminController::class, 'users']);
    Route::get('users/{id}', [AdminController::class, 'showUser']);
    Route::patch('users/{id}/status', [AdminController::class, 'updateUserStatus']);
    Route::delete('users/{id}', [AdminController::class, 'deleteUser']);
    Route::get('students', [AdminController::class, 'students']);
    Route::get('companies', [AdminController::class, 'companies']);
    Route::get('jobs', [AdminController::class, 'jobs']);
    Route::get('applications', [AdminController::class, 'applications']);
});
