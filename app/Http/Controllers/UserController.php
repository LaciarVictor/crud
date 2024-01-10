<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequests\UserCreateRequest;
use App\Http\Requests\UserRequests\UserUpdateRequest;
use App\Http\Requests\UserRequests\UserRegisterRequest;

use App\Models\User;
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
        //$this->authorizeResource(User::class, 'user');

        
    }




    /**
     * Trata de mostrar la lista de usuarios paginada.
     *
     * @return ?LengthAwarePaginator
     */
    public function index(): LengthAwarePaginator | JsonResponse
    {
        $this->authorize('index', User::class);

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
        $this->authorize('store', User::class);
        return $this->userService->userCreate($request);

    }



    /**
     * Trata de registrar un usuario. Diferencias con store:
     * Asigna el rol invitado (guest).
     * Genera un token.
     *
     * @param UserRegisterRequest $request
     * @return JsonResponse
     */
    public function register(UserRegisterRequest $request):JsonResponse
    {

        $this->authorize('register', User::class);
        return $this->userService->userRegister($request);


    }




    /**
     * Trata de devolver un usuario proporcionando su id.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id):JsonResponse
    {
        $this->authorize('show', User::class);

        return $this->userService->findUser($id);

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
        $user = User::findOrFail($id);
        $this->authorize('update', $user);
        return $this->userService->userUpdate($request,$id);
    }




    /**
     * Trata de borrar un usuario por su id.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id):JsonResponse
    {
        $this->authorize('destroy', User::class);
        return $this->userService->deleteUser($id);
    }

}
