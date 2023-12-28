<?php

namespace App\Services;

use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Http\Requests\UserRequests\UserCreateRequest;
use App\Http\Requests\UserRequests\UserUpdateRequest;
use App\Http\Requests\UserRequests\UserLoginRequest;
use App\Interfaces\ICrudable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Hash;
use App\Services\CrudService;
use App\Services\AuthService;



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
     * @return array
     */
    public function create(object $request): array
    {

        //Crea un instancia del modelo y le asigna los valores
        //exceptua password y role para tratarlos aparte.
        $user = $this->buildModelInstance($request, [
            'password',
            'role',
        ]);

        // Encriptar el password y asignarlo al usuario.
        $user->password = Hash::make($request->input('password'));

        $role_id = $request->input('role');

        $this->assignUserRole($user, $role_id);
        $user->save();

        return $this->setFormatResponse($user);
    }




    /**
     * Registra a un usuario.
     *
     * @param UserCreateRequest $request
     * @return void
     */
    public function register(UserCreateRequest $request): void
    {


        $userData = $this->create($request);

        $user = $this->findModelById($userData['id']);

        $userLoginRequest = new UserLoginRequest([
            'user' => $user->userName,
            'password' => $request->input('password')
        ]);

        $this->authservice->login($userLoginRequest);
    }




    public function updateUser(UserUpdateRequest $request, int $id): array
    {
        $user = $this->findModelById($id);

        $role_id = $request->input('role');

        $this->assignUserRole($user, $role_id);

        $data = $request->except('role');
        $data['password'] = Hash::make($request->input('password'));

        parent::update($id, $data);


        return $this->setFormatResponse($user);
    }




    public function deleteUser(int $id): void
    {
        $user = $this->findModelById($id);
        $user->roles()->detach();
        $user->delete();
        //TO DO borrar el token
    }




    public function findAllModels($perPage = 10): ?LengthAwarePaginator
    {
        try {
            //$users = $this->model->with('roles:id')->get();
            $usersPaginator = $this->model->with('roles:id')->paginate($perPage);

            if ($usersPaginator->isEmpty()) {
                return null; // Retorna null si la tabla está vacía
            }

                $formattedUsers = collect($usersPaginator->items())->map(function ($user) {
                    return $this->setFormatResponse($user);
                });


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
        } catch (\Exception $e) {
            //TO DO function type expected LengthAwarePaginator instead of json response. Fix it.
            return null;
        }
    }




    public function findModelById(int $userId): ?User
    {
        try {
            $user = $this->model->with('roles:id')->findOrFail($userId);
            return $this->setFormatResponse($user);
        } catch (ModelNotFoundException $exception) {
            return null;
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }




    protected function assignUserRole(User $user, int $roleId): void
    {
        $user->roles()->detach();

        $guestRoleId = 8;
        $role = Role::findById($roleId) ?? Role::findById($guestRoleId);

        if ($role) {
            $user->assignRole($role);
        }
    }




    protected function deleteUserRole(User $usr): void
    {
        $usr->roles()->detach(); // Desasignar todos los roles del usuario al eliminarlo
    }




    public function buildModelInstance(object $modelData, array $exceptCollection): User
    {
        try {

            $model = new User();

            foreach ($modelData->all() as $key => $value) {
                if (!in_array($key, $exceptCollection)) {
                    $model->{$key} = $value;
                }
            }

            return $model;
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }




    public function setFormatResponse($userData): array
    {
        $user = $userData;
        $role = $user->roles->first();

        return [
            'id' => $user->id,
            'userName' => $user->userName,
            'firstName' => optional($user->firstName)->id,
            'lastName' => optional($user->lastName)->id,
            'phoneCode' => optional($user->phoneCode)->id,
            'phoneNumber' => optional($user->phoneNumber)->id,
            'email' => $user->email,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,

            'role' => $role ? $role->id : null
        ];
    }
}
