<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get( '/', [HomeController::class, 'index'] )->name( 'home' );
Route::get( '/account/register', [UserController::class, 'registration'] )->name( 'account.registration' );
Route::post( '/account/process-register', [UserController::class, 'processRegistration'] )->name( 'account.processRegistration' );
Route::get( '/login', [UserController::class, 'login'] )->name( 'account.login' );
