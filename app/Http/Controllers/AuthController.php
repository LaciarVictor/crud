<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Requests\UserRequest;
use App\Services\RegistrationService;

class AuthController extends Controller
{


    protected $registrationService;

    public function __construct(RegistrationService $registrationService)
    {

        $this->registrationService = $registrationService;
    }




    public function register(UserRequest $request)
    {
        try {

            $user = $this->registrationService->createUser($request);
            $this->registrationService->assignRoleToUser($user, $request);

            $user->save();

            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json(['token' => $token], 201);
        } catch (\Exception $e) {

            return response()->json(['message' => 'Error creando el registro', 'error' => $e->getMessage(),], 500);
        }
    }




    public function login(UserRequest $request)
    {
        try {
            $credentials = $request->validated();

            if (!Auth::attempt($credentials)) {
                return response()->json(['message' => 'Usuario o contraseña incorrectos.'], 401);
            }

            $user = $request->user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json(['token' => $token], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error en el proceso de autenticación.'], 500);
        }
    }




    public function logout(UserRequest $request)
    {
        try {
            $user = $request->user();
            $user->currentAccessToken()->delete();

            return response()->json(['message' => 'Logout correcto.'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error en el proceso de cierre de sesión.'], 500);
        }
    }
}
