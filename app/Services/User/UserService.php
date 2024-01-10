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
use DateTime;
use Illuminate\Support\Facades\Hash;

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
    protected $customLengthAwarePaginator;

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


            list($processedRequest, $role) = $this->processUserRequest($request);

            $user = parent::create($processedRequest);

            $this->setRole($role, $user);

            return response()->json([$this->setJSONResponse($user)]);
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

            $userPassword = $request->input('password');

            list($processedRequest, $role) = $this->processUserRequest($request);

            $user = parent::create($processedRequest);
            $this->setRole($role, $user);

            //Crear una solicitud para loguear al usuario.
            $userLoginRequest = new UserLoginRequest([
                'user_name' => $user->user_name,
                'password' => $userPassword
            ]);

            // Loguear al usuario y obtener la respuesta JSON.
            $loginResponse = $this->authservice->login($userLoginRequest);

            // Extraer el token de la respuesta JSON.
            $token = $loginResponse->original['token'];

            // Agregar el token al array del usuario.
            $userArray = $this->setJSONResponse($user);
            $userArray['token'] = $token;

            // Devolver el usuario y el token en el mismo array JSON.
            return response()->json($userArray);
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

            list($processedRequest, $role) = $this->processUserRequest($request);

            $user = parent::update($id, $processedRequest);
            $this->setRole($role, $user);


            if ($user) {
                return response()->json($this->setJSONResponse($user));
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
     * @return  LengthAwarePaginator
     * @return JsonResponse
     * @throws \Exception Si ocurre un error durante la ejecución de la función.
     */
         public function findAllModels($perPage = 10): LengthAwarePaginator | JsonResponse
    {
        try {
            //Busca todos los usuarios y los pagina
            $usersPaginator = $this->model->with('roles:id')->paginate($perPage);

            if ($usersPaginator->isEmpty()) {
                return response()->json(['message' => 'No hay usuarios registrados.']); 
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



/**
 * Undocumented function
 *
 * @param integer $userId
 * @return JsonResponse
 */
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

        $role_id = $user->roles->first()->id;

        $role_name = Role::where('id', $role_id)->first()->name;


        return [
            'id' => $user->id,
            'user_name' => $user->user_name,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'phone_code' => $user->phone_code,
            'phone_number' => $user->phone_number,
            'email' => $user->email,
            'created_at' => $this->formattedDate($user->created_at),
            'updated_at' => $this->formattedDate($user->updated_at),
            'role' => $role_name
        ];
    }



    /**
     * Hashea el password del request y elimina el rol del request.
     *
     * @param UserCreateRequest $request
     * @return mixed
     * @throws Exception
     */
    private function processUserRequest(object $request): array
    {
        // Obtener el rol y el password del request
        $role = $request->input('role');
        $password = $request->input('password');

        // Hashear el password
        if ($password) {
            $hashedPassword = Hash::make($password);
            // Modificar el request original
            $request->merge(['password' => $hashedPassword]);
        }
        if ($role) {
            $request->offsetUnset('role');
            return [$request, $role];
        }

        return [$request, null];
    }

    function formattedDate($timestamp): string
     {
        // Crear un objeto DateTime a partir del timestamp
        $date = new DateTime($timestamp);
    
        // Reformatear la fecha al formato dd:mm:yyyy
        return $date->format('d/m/Y');
    }



    /**
     * Asigna el rol al usuario.
     *
     * @param ?string $role El rol del usuario, puede ser nulo.
     * @param User $user
     * @return void
     */
    private function setRole(?string $role, User $user): void
    {

        if($user->roles->count() > 0){
            $user->syncRoles($role?: 'guest');
        }
        
        $user->assignRole($role?: 'guest');
    }
    
}
