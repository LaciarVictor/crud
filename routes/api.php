<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;

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


//AUTH ROUTES
Route::middleware('auth:sanctum')->group(function () {

});

Route::get('/user/index', [UserController::class, 'index'])->name('user.index');
Route::post('/user/create', [UserController::class, 'create'])->name('user.create');
Route::put('/user/update/{id}', [UserController::class, 'update'])->name('user.update');
Route::get('/user/read/{id}', [UserController::class, 'read'])->name('user.read');
Route::delete('/user/delete/{id}', [UserController::class, 'delete'])->name('user.delete');
Route::get('/user/search/{name}', [UserController::class, 'search'])->name('user.search');
Route::get('/user/hintSearch/{hint}', [UserController::class, 'hintSearch'])->name('user.hintSearch');

Route::apiResource('roles', RoleController::class);

// Route::apiResource('user', UserController::class);
