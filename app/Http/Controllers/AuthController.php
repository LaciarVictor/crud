<?php

namespace App\Http\Controllers;


use App\Http\Requests\UserRequests\UserLoginRequest;
//use App\Http\Requests\UserRequests\UserRegisterRequest;
//use App\Http\Requests\UserRequests\UserStoreRequest;
//use Illuminate\Support\Facades\Auth;
use App\Services\AuthService;
//use App\Models\User;
//use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{


    protected $authService;

    public function __construct(AuthService $authService)
    {

        $this->authService = $authService;
    }




    /**
     * Verifica las credenciales del usuario, crea el token y envía el JSON.
     */
    public function login(UserLoginRequest $request)
    {
        try {

            return $this->authService->login($request);

        //     //Buscar el usuario en la base de datos.
        //     $user = User::where('name', $request['name'])->first();

        //     //Si el usuario no existe o la contraseña es inválida retornar un error
        //     if (!$user || !Hash::check($request['password'], $user->password)) {
        //         return response()->json([
        //             'message' => 'Usuario o contraseña incorrectos.'
        //         ], 401);
        //     }
        // //devolver el token
        // return $this->getToken ($user);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Error en el proceso de autenticación.', 'error'=> $e->getMessage()], 500);
        }
    }



    /**
     *  Verifica las credenciales del usuario, borra el token y envía el JSON.
     */
    public function logout()
    {
        try 
        {
            return $this->authService->logout();
            // //buscar el token
            // $token = Auth::guard('sanctum')->user();

            // //el token es válido
            // if ($token !== null) 
            // {

            //     //Buscar el usuario y borrarle el token.
            //     $user = User::where('name', $token->name)->first();
            //     $user->tokens()->delete();
            //     return response()->json(['message' => 'Usuario deslogueado.'], 200);

            // } 
            // else 
            // {
            
            //     return response()->json(['message' => 'El token no es válido.'], 401);

            // }
        } 
        catch (\Exception $e) 
        {

            return response()->json(['message' => 'Error en el proceso de cierre de sesión.'], 500);
        }
    }


}
