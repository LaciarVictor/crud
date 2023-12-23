<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TokenVerificationController;

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
//probar el token
Route::post('/verify-token',[TokenVerificationController::class, 'verify'])->name('verify');


//Estas rutas deberían ser de libre acceso para cualquiera que visite el sitio.
Route::post('/register', [UserController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');



//AUTH ROUTES
Route::middleware('auth:sanctum','AuthMiddleware')->group(function () {

//Estas rutas son sólo para usuarios autenticados.
    Route::apiResource('users', UserController::class);
    Route::apiResource('roles', RoleController::class);

});
//Aquí debería poner un grupo de rutas con middleware para los usuarios con el rol guest.