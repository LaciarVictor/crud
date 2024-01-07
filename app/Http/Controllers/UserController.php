<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequests\UserCreateRequest;
use App\Http\Requests\UserRequests\UserUpdateRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Services\User\UserService;
use App\Services\User\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;



/**
 * Esta clase controla al modelo User
 */
class UserController extends Controller
{
   

    protected $userService;
    protected $authService;

    /**
     * El constructor utiliza dos servicios. El user service para el crud de usuarios
     * y el auth service para la autenticación.
     *
     * @param UserService $userService
     * @param AuthService $authService
     */
    public function __construct(UserService $userService, AuthService $authService)
    {

        $this->userService = $userService;
        $this->authService = $authService;


        /**
         * utilizar UserPolicy para gestionar los permisos de acceso a los métodos del controlador.
         */
        $this->authorizeResource(User::class, 'user');

        
    }




    /**
     * Trata de mostrar la lista de usuarios paginada.
     *
     * @return ?LengthAwarePaginator
     */
    public function index(): ?LengthAwarePaginator
    {
        try {

            $perPage = request()->input('perPage', 10);
            $usersPaginated = $this->userService->findAllModels($perPage);

            return $usersPaginated;

        } catch (\Exception $e) {

            return response()->json(['message' => 'Error al recuperar usuarios', 
            'error' => $e->getMessage(),], 500);
        }
    }




/**
 * Trata de guardar un usuario con un rol asignado
 *
 * @param UserCreateRequest $request
 * @return JsonResponse
 */
    public function store(UserCreateRequest $request):JsonResponse
    {
        try {


            $this->userService->create($request);
            return response()->json(['message' => 'Usuario creado correctamente.']);


        } catch (\Exception $e) {

            return response()->json(['message' => 'Error creando usuario', 
            'error' => $e->getMessage(),], 500);
        }
    }



    /**
     * Trata de registrar un usuario. Diferencias con store:
     * Asigna el rol invitado (guest).
     * Genera un token.
     *
     * @param UserCreateRequest $request
     * @return JsonResponse
     */
    public function register(UserCreateRequest $request):JsonResponse
    {
        try {

            $this->userService->register($request);

            //$user = $this->userService->register($request);

           // return response()->json(['token' => $token]);

        } catch (\Exception $e) {

            return response()->json(['message' => 'Error registrando usuario', 'error' => $e->getMessage()], 500);       
         }
    }




    /**
     * Trata de devolver un usuario proporcionando su id.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id):JsonResponse
    {
        try {

            return response()->json(['message' => 'Usuario encontrado.', 
            'Usuario' =>$this->userService->findModelById($id)]);

        } catch (ModelNotFoundException $e) {

            return response()->json(['message' => 'Usuario no encontrado.',], 404);
        }
    }




    /**
     * Trata de actualizar un usuario en la base de datos.
     *
     * @param UserUpdateRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UserUpdateRequest $request, int $id):JsonResponse
    {
        try {

            return response()->json(['message' => 'Usuario actualizado correctamente.', 
            'Usuario' =>$this->userService->updateUser($request, $id)]);

        } catch (ModelNotFoundException $e) {

            return response()->json(['message' => 'Usuario no encontrado.',], 404);
        } catch (\Exception $e) {

            return response()->json(['message' => 'Error actualizando usuario.', 'error' => $e->getMessage(),], 500);
        }
    }




    /**
     * Trata de borrar un usuario por su id.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id):JsonResponse
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
