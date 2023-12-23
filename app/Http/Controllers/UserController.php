<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequests\UserRegisterRequest;
use App\Http\Requests\UserRequests\UserStoreRequest;
use App\Http\Requests\UserRequests\UserUpdateRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Services\UserService;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

/*
*UserController: Esta clase se encargará de gestionar las operaciones relacionadas 
*con los usuarios, como la creación, actualización y eliminación de usuarios.
*/


class UserController extends Controller
{
   

    protected $userService;
    protected $authService;

    public function __construct(UserService $userService, AuthService $authService)
    {

        $this->userService = $userService;
        $this->authService = $authService;

        //utilizar UserPolicy para gestionar los permisos de acceso a los métodos del controlador.
        $this->authorizeResource(User::class, 'user');

        
    }




    /**
     * Muestra la lista de usuarios.
     */
    public function index()
    {
        try {

            return response()->json($this->userService->getAllUsers());

        } catch (\Exception $e) {

            return response()->json(['message' => 'Error al recuperar usuarios', 
            'error' => $e->getMessage(),], 500);
        }
    }




    /**
     * Guarda un nuevo usuario creado por un usuario con privilegios.
     * Esta función se utiliza en el dashboard de usuarios.
     */
    public function store(UserStoreRequest $request):JsonResponse
    {
        try {


            return response()->json(['message' => 'Usuario creado correctamente.', 
            'Usuario' =>$this->userService->createUser($request)]);


        } catch (\Exception $e) {

            return response()->json(['message' => 'Error creando usuario', 
            'error' => $e->getMessage(),], 500);
        }
    }




    public function register(UserRegisterRequest $request):JsonResponse
    {
        try {


            $user = $this->userService->registerUser($request);
            $token = $this->authService->createUserAccessToken($user);
            return response()->json(['token'=> $token]);


        } catch (\Exception $e) {

            return response()->json(['message' => 'Error registrando usuario', 'error' => $e->getMessage(),], 500);
        }
    }



    /**
     * Devuelve un usuario determinado por su número de id.
     */
    public function show(string $id):JsonResponse
    {
        try {

            return response()->json(['message' => 'Usuario encontrado.', 
            'Usuario' =>$this->userService->getUser($id)]);

        } catch (ModelNotFoundException $e) {

            return response()->json(['message' => 'Usuario no encontrado.',], 404);
        }
    }




    /**
     * Actualiza un usuario específico en la base de datos.
     */
    public function update(UserUpdateRequest $request, string $id):JsonResponse
    {
        try {

            return response()->json(['message' => 'Usuario actualizado correctamente.', 
            'Usuario' =>$this->userService->updateUser($request, $id)]);

        } catch (ModelNotFoundException $e) {

            return response()->json(['message' => 'Usuario no encontrado.',], 404);
        }
    }




    /**
     * Remueve un usuario específico de la base de datos.
     */
    public function destroy(string $id):JsonResponse
    {
        try {
            $this->userService->deleteUser($id);

            return response()->json(['message' => 'Usuario borrado correctamente.',], 200);
        } catch (ModelNotFoundException $e) {

            return response()->json(['message' => 'Usuario no encontrado.',], 404);
        } catch (\Exception $e) {

            return response()->json(['message' => 'Error borrando usuario.', 'error' => $e->getMessage(),], 500);
        }
    }


}
