<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Http\Requests\UserRequests\UserLoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Laravel\Sanctum\Sanctum;
use Laravel\Sanctum\PersonalAccessToken;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AuthService
{
    
    public function login(UserLoginRequest $request): JsonResponse
{
    // Validar las credenciales del usuario y generar el token de acceso
    $credentials = $request->only(['name', 'password']);

    if (!Auth::attempt($credentials)) {
        return response()->json(['message' => 'Usuario o contraseña incorrectos.'], 401);
    }

    // Obtener el usuario autenticado
    $user = Auth::user();
    $cantidadTokens = $user->tokens->count();
    // Verificar si el usuario ya tiene un token de acceso activo
    if ($cantidadTokens > 0) {
        
        return response()->json(['message' => 'El usuario ya tiene una sesión activa.'], 403);
    }

    // Crear un nuevo token de acceso para el usuario
    $accessToken = $this->createUserAccessToken($user);

    // Devolver el token de acceso
    return response()->json(['token' => $accessToken]);
}




public function logout(Request $request)
{
    $token = $request->bearerToken();

    $personalAccessToken = PersonalAccessToken::findToken($token);

    if ($personalAccessToken) {
        $user = $personalAccessToken->tokenable;
        $user->tokens()->delete();

        return response()->json(['message' => 'Cierre de sesión exitoso.']);
    }
    
    return response()->json(['message' => 'Token inválido']);

    // $tokenDetectado = $request->bearerToken();

    // $DBToken = PersonalAccessToken::findToken($tokenDetectado);

    // if($DBToken){

    //     $userId = $DBToken->tokenable_id;
    //     $user = User::where('id',$userId)->first();
    //     $user->tokens()->delete();
    //     return response()->json(['Message' => 'Cierre de sesión exitoso.']);
    // }else{

    //     return response()->json(['Message' => 'Token inválido']);

    // }


    

  //$request->user()->currentAccessToken()->delete();
  //$user = User::where('id',1003)->first();
 
  //$user->tokens()->delete();
  //return response()->json(['user'=>$user]);

}


public function createUserAccessToken(User $user): string
{
    // Devuelve el token existente si ya tiene uno
    $existingToken = $user->currentAccessToken();

    if ($existingToken !== null) {
        return $existingToken->plainTextToken;
    }

    // Generar un nuevo token de acceso
    $token = $user->createToken('token');

    // Agregar una expiración de 30 minutos al token
    $this->updateTokenExpiration($token->accessToken);

    return $token->plainTextToken;
}




    public function updateTokenExpiration($token): void
    {
        $token->forceFill([
            'expires_at' => Carbon::now()->addMinutes(30)
        ])->save();
        
    }




    public function updateUserTokenExpiration(Request $request): void
    {
        $user = $request->user();
    
        if( $user ){           
            $token = $user->currentAccessToken();
            $this->updateTokenExpiration($token);
        }
    }
}