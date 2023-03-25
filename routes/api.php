<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\UserController;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


// Route::group(['middleware'=>'api'],function($routes){
//     Route::post('/register',[AuthController::class,'register']);
//     Route::post('/login',[AuthController::class,'login']);
// });



Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'login');
    Route::post('/register', 'register');
    Route::post('/logout', 'logout');
});

Route::controller(TodoController::class)->group(function () {
    Route::get('/todos', 'index');
    Route::get('/todos_paging', 'index_paging');
    Route::get('/todo_by_user_id', 'todo_by_user_id');
    Route::post('/todo', 'store');
    Route::get('/todo/{id}', 'show');
    Route::put('/todo/{id}', 'update');
    Route::delete('/todo/{id}', 'destroy');
}); 


Route::controller(UserController::class)->group(function () {
    Route::get('/profile', 'profile');
});


