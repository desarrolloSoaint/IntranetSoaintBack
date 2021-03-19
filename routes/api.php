<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RuleController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\AccessControlController;
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

// AUTH
Route::post('register',[UserController::class, 'register']);
Route::post('login',[UserController::class, 'login']);
// ROLES
Route::get('getRoles',[RoleController::class, 'getRoles']); 

// Need Authentication
Route::group(['middleware' => 'jwt.auth'], function () {
    // USER
    Route::post('logout',[UserController::class, 'logout']);
    Route::post('refreshToken',[UserController::class, 'refreshToken']);
    Route::post('getUserRole',[UserController::class, 'getUserRole']);
    Route::get('getUsers',[UserController::class, 'getUsers']);
    Route::get('countOfUsers',[UserController::class, 'countOfUsers']);
    // ROLES
    Route::post('addRole',[RoleController::class, 'addRole']);
    // RULES
    Route::get('getRules',[RuleController::class, 'index']);
    Route::get('countOfRules',[RuleController::class, 'countOfRules']);
    Route::post('addRules',[RuleController::class, 'store']);
    Route::get('showRule/{id}',[RuleController::class, 'show']);
    Route::delete('deleteRule/{id}',[RuleController::class, 'destroy']);
    Route::post('restoreRule/{id}',[RuleController::class, 'restore']);
    // SCHEDULES
    Route::get('getSchedules',[ScheduleController::class, 'index']);
    Route::get('countOfSchedules',[ScheduleController::class, 'countOfSchedules']);
    Route::get('showSchedule/{id}',[ScheduleController::class, 'show']);
    Route::post('addSchedule',[ScheduleController::class, 'store']);
    Route::put('updateSchedule/{id}',[ScheduleController::class, 'update']);
    Route::delete('deleteSchedule/{id}',[ScheduleController::class, 'destroy']);
    Route::post('restoreSchedule/{id}',[ScheduleController::class, 'restore']);
    Route::post('statusSchedule/{id}',[ScheduleController::class, 'changeStatus']);
    Route::get('activeSchedules',[ScheduleController::class, 'activeSchedules']);
    Route::get('inactiveSchedules',[ScheduleController::class, 'inactiveSchedules']);
    //ACCESS CONTROL
    Route::get('getHistory',[AccessControlController::class, 'getHistory']);
    Route::post('getCurrentUserHistory',[AccessControlController::class, 'getCurrentUserHistory']);
    Route::post('getHistoryByUserAndRule',[AccessControlController::class, 'getHistoryByUserAndRule']);
    Route::post('registerStartTime',[AccessControlController::class, 'registerStartTime']);    
    Route::post('registerFinishTime',[AccessControlController::class, 'registerFinishTime']);
    Route::post('addObservation',[AccessControlController::class, 'addObservation']);
    Route::get('clearHistory',[AccessControlController::class, 'clearHistory']);
    Route::get('export',[AccessControlController::class,'export']);
});