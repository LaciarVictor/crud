<?php

namespace App\Http\Controllers;


use App\Http\Requests\UserRequests\UserLoginRequest;
use App\Http\Requests\UserRequests\UserLogoutRequest;
use App\Http\Requests\UserRequests\UserRegisterRequest;
use App\Http\Requests\UserRequests\UserStoreRequest;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\Sanctum;
use App\Services\RegistrationService;
use App\Models\User;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{


    protected $registrationService;

    public function __construct(RegistrationService $registrationService)
    {

        $this->registrationService = $registrationService;
    }




    /**
     * Guarda un nuevo usuario creado por el usuario (rol invitado).
     * Esta función se utiliza en el registro en el welcome.
     */
    public function register(UserRegisterRequest $request)
    {
        //Crear un request especial con el rol de invitado para el usuario recién registrado
        $storeRequest = new UserStoreRequest($request->except('role'));
        $storeRequest->merge(['role' => 1]);


        //Crear el usuario.
        $user = $this->registrationService->createUser($storeRequest);

        //Asignarle el rol
        $this->registrationService->assignRoleToUser($user, $storeRequest);

        //Guardar en la base de datos.
        $user->save();


        // Generar el token de acceso
        Sanctum::actingAs($user);
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Registro exitoso.',
            'user' => $user,
            'access_token' => $token,
        ], 200);
    }




    /**
     * Verifica las credenciales del usuario, crea el token y envía el JSON.
     */
    public function login(UserLogoutRequest $request)
    {
        try {

            //Buscar el usuario en la base de datos.
            $user = User::where('name', $request['name'])->first();

            //Si el usuario no existe o la contraseña es inválida retornar un error
            if (!$user || !Hash::check($request['password'], $user->password)) {
                return response([
                    'msg' => 'Usuario o contraseña incorrectos.'
                ], 401);
            }
            //La autenticación es válida. Crear un token.
            $token = $user->createToken('auth-token')->plainTextToken;

            return response()->json(['token' => $token], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error en el proceso de autenticación.'], 500);
        }
    }



    /**
     *  Verifica las credenciales del usuario, borra el token y envía el JSON.
     */
    public function logout()
    {
        try 
        {
            $token = Auth::guard('sanctum')->user();

            //si el token es válido
            if ($token !== null) 
            {

                //Buscar el usuario y borrarle el token.
                $user = User::where('name', $token->name)->first();
                $user->tokens()->delete();
                return response()->json(['message' => 'Usuario deslogueado.'], 200);

            } 
            else 
            {
            
                return response()->json(['message' => 'El token no es válido.'], 500);

            }
        } 
        catch (\Exception $e) 
        {

            return response()->json(['message' => 'Error en el proceso de cierre de sesión.'], 500);
        }
    }
}
