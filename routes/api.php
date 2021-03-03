<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RuleController;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('register',[UserController::class, 'register']);
Route::post('login',[UserController::class, 'login']);
Route::get('getRoles',[RoleController::class, 'getRoles']);


Route::group(['middleware' => 'jwt.auth'], function () {
    Route::post('logout',[UserController::class, 'logout']);
    Route::post('refreshToken',[UserController::class, 'refreshToken']);
    Route::post('addRole',[RoleController::class, 'addRole']);
    Route::post('getUserRole',[UserController::class, 'getUserRole']);
    Route::get('getRules',[RuleController::class, 'index']);
    Route::post('addRules',[RuleController::class, 'store']);    
});