<?php

namespace App\Services\User;

use App\Interfaces\ICrudable;

use App\Services\CrudService;
use App\Services\User\AuthService;

use App\Models\User;
use Spatie\Permission\Models\Role;

use App\Http\Requests\UserRequests\UserCreateRequest;
use App\Http\Requests\UserRequests\UserUpdateRequest;
use App\Http\Requests\UserRequests\UserLoginRequest;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Exception;
use Throwable;



class UserService extends CrudService implements ICrudable
{
    protected $authservice;

    public function __construct(User $user, AuthService $authService)
    {
        parent::__construct($user);
        $this->authservice = $authService;
    }




    /**
     * Crea un nuevo usuario.
     * 
     * @param UserCreateRequest $request
     * @return JsonResponse
     */
    public function userCreate(UserCreateRequest $request): JsonResponse
    {
        try {

            //Crear el usuario.
            $user = parent::create($request);

            return response()->json([
                'user' => $this->setJSONResponse($user)
            ]);
        } catch (ValidationException $ex) {

            return response()->json(['message' => $ex->validator->errors()], 422);
        } catch (Exception $ex) {

            return response()->json(['message' => $ex->getMessage()], 500);
        } catch (Throwable $th) {

            return response()->json(['message' => $th->getMessage()], 500);
        }
    }





    /**
     * Registra a un usuario.
     *
     * @param UserCreateRequest $request
     * @return JsonResponse
     */
    public function UserRegister(UserCreateRequest $request): JsonResponse
    {

        try {

            //Crear el usuario.
            $user = parent::create($request);

            //Crear una solicitud para loguear al usuario.
            $userLoginRequest = new UserLoginRequest([
                'user' => $user->userName,
                'password' => $request->input('password')
            ]);

            // Loguear al usuario y obtener el token.
            $token = $this->authservice->login($userLoginRequest);

            // Devolver el usuario y el token en la misma respuesta JSON.
            return response()->json([
                'user' => $this->setJSONResponse($user),
                'token' => $token,
            ]);
        } catch (ValidationException $ex) {

            return response()->json(['message' => $ex->validator->errors()], 422);
        } catch (Exception $ex) {

            return response()->json(['message' => $ex->getMessage()], 500);
        } catch (Throwable $th) {

            return response()->json(['message' => $th->getMessage()], 500);
        }
    }




    public function userUpdate(UserUpdateRequest $request, int $id): JsonResponse
    {
        try {

            //Crear el usuario.
            $user = parent::update($id, $request);

            if ($user) {
                return $this->setJSONResponse($user);
            } else {
                throw new ModelNotFoundException('No se encontró el usuario');
            }
        } catch (ModelNotFoundException $ex) {

            return response()->json(['message' => $ex->getMessage()], 404);
        } catch (ValidationException $ex) {

            return response()->json(['message' => $ex->validator->errors()], 422);
        } catch (Exception $ex) {

            return response()->json(['message' => $ex->getMessage()], 500);
        } catch (Throwable $th) {

            return response()->json(['message' => $th->getMessage()], 500);
        }
    }





    public function deleteUser(int $id): JsonResponse
    {
        try {
            $success = parent::delete($id);

            return response()->json(['message' => $success ? 'Usuario eliminado correctamente.' : 'No se encontró el usuario.'], $success ? 200 : 404);
        } catch (ValidationException $ex) {
            return response()->json(['message' => $ex->validator->errors()], 422);
        } catch (Exception $ex) {

            return response()->json(['message' => $ex->getMessage()], 500);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }



    /**
     * Find all models.
     *
     * @param int $perPage El número de usuarios por página (por defecto: 10)
     * @return LengthAwarePaginator|null Los usuarios paginados o null si la tabla está vacía
     * @throws \Exception Si ocurre un error durante la ejecución de la función.
     */
    public function findAllModels($perPage = 10): ?LengthAwarePaginator
    {
        try {
            //Busca todos los usuarios y los pagina
            $usersPaginator = $this->model->with('roles:id')->paginate($perPage);

            if ($usersPaginator->isEmpty()) {
                return null; // Retorna null si la tabla está vacía
            }
            // Formatea los usuarios para que el rol aparezca en la misma llave.
            $formattedUsers = collect($usersPaginator->items())->map(function ($user) {
                return $this->setJSONResponse($user);
            });

            //Agrega la paginación. El total de páginas, la petición actual 
            //la petición anterior, la petición siguiente y la petición final.
            $paginator = new LengthAwarePaginator(
                $formattedUsers,
                $usersPaginator->total(),
                $usersPaginator->perPage(),
                $usersPaginator->currentPage(),
                [
                    'path' => Paginator::resolveCurrentPath(),
                    'query' => request()->query(),
                ]
            );

            return $paginator;
        } catch (ValidationException $ex) {
            return response()->json(['message' => $ex->validator->errors()], 422);
        } catch (Exception $ex) {

            return response()->json(['message' => $ex->getMessage()], 500);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }




    public function findUser(int $userId): JsonResponse
    {
        try {
            $user = $this->model->with('roles:id')->findOrFail($userId);
            return $this->setJSONResponse($user);
        } catch (ValidationException $ex) {
            return response()->json(['message' => $ex->validator->errors()], 422);
        } catch (Exception $ex) {

            return response()->json(['message' => $ex->getMessage()], 500);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }




    public function setJSONResponse($userData): array
    {
        $user = $userData;
        $role = $user->roles->first();

        return [
            'id' => $user->id,
            'user_name' => $user->user_name,
            'first_name' => optional($user->first_name)->id,
            'last_name' => optional($user->last_name)->id,
            'phone_code' => optional($user->phone_code)->id,
            'phone_number' => optional($user->phone_number)->id,
            'email' => $user->email,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
            'role' => $role->name
        ];
    }
}
