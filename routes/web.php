<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JobsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
 */

Route::get( '/', [HomeController::class, 'index'] )->name( 'home' );
Route::get( '/jobs', [JobsController::class, 'index'] )->name( 'jobs' );
Route::get( '/jobs/detail/{id}', [JobsController::class, 'detail'] )->name( 'jobDetail' );
Route::post( '/apply-job', [JobsController::class, 'applyJob'] )->name( 'applyJob' );
Route::post( '/save-job', [JobsController::class, 'saveJob'] )->name( 'saveJob' );

Route::group( ['prefix' => 'admin', 'middleware' => 'checkRole'], function () {
    Route::get( '/dashboard', [DashboardController::class, 'index'] )->name( 'admin.dashboard' );
} );

Route::group( ['prefix' => 'account'], function () {
    // Guest Routes
    Route::group( ['middleware' => 'guest'], function () {
        Route::get( '/register', [UserController::class, 'registration'] )->name( 'account.registration' );
        Route::post( '/process-register', [UserController::class, 'processRegistration'] )->name( 'account.processRegistration' );
        Route::get( '/login', [UserController::class, 'login'] )->name( 'account.login' );
        Route::post( '/authenticate', [UserController::class, 'authenticate'] )->name( 'account.authenticate' );
    } );

    // Authenticated Routes
    Route::group( ['middleware' => 'auth'], function () {
        Route::get( '/profile', [UserController::class, 'profile'] )->name( 'account.profile' );
        Route::put( '/update-profile', [UserController::class, 'updateProfile'] )->name( 'account.updateProfile' );
        Route::post( '/update-profile-pic', [UserController::class, 'updateProfilePic'] )->name( 'account.updateProfilePic' );
        Route::get( '/logout', [UserController::class, 'logout'] )->name( 'account.logout' );
        Route::get( '/create-job', [UserController::class, 'createJob'] )->name( 'account.createJob' );
        Route::post( '/save-job', [UserController::class, 'saveJob'] )->name( 'account.saveJob' );
        Route::get( '/my-jobs', [UserController::class, 'myJobs'] )->name( 'account.myJobs' );
        Route::get( '/my-jobs/edit/{jobId}', [UserController::class, 'editJob'] )->name( 'account.editJob' );
        Route::post( '/update-job/{jobId}', [UserController::class, 'updateJob'] )->name( 'account.updateJob' );
        Route::post( '/delete-job', [UserController::class, 'deleteJob'] )->name( 'account.deleteJob' );
        Route::get( '/my-job-applications', [UserController::class, 'myJobApplications'] )->name( 'account.myJobApplications' );
        Route::post( '/remove-job-application', [UserController::class, 'removeJobs'] )->name( 'account.removeJobs' );
        Route::get( '/saved-jobs', [UserController::class, 'savedJobs'] )->name( 'account.savedJobs' );
        Route::post( '/remove-saved-job', [UserController::class, 'removeSavedJob'] )->name( 'account.removeSavedJob' );
        Route::post( '/update-password', [UserController::class, 'updatePassword'] )->name( 'account.updatePassword' );
    } );
} );
