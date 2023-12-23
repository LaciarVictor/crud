<?php

namespace App\Services;

use App\Models\User;
use App\Http\Requests\UserRequests\UserRegisterRequest;
use App\Http\Requests\UserRequests\UserStoreRequest;
use App\Http\Requests\UserRequests\UserUpdateRequest;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;


class UserService
{


    public function createUser(UserStoreRequest $request): array
    {
        // Obtener los datos del request
        $userData = $request->validated();

        // Crear un nuevo usuario con los datos proporcionados
        $user = new User();
        $user->name = $userData['name'];
        $user->email = $userData['email'];
        $user->password = Hash::make($userData['password']);
        $user->save();

        // Asignar el rol correspondiente al usuario
        $this->assignRoleToUser($user, $userData['role']);

        return $this->JSONmaker($user);
    }




    public function registerUser(UserRegisterRequest $request): User
    {
        // Obtener los datos del request
        $userData = $request->validated();

        // Crear un nuevo usuario con los datos proporcionados
        $user = new User();
        $user->name = $userData['name'];
        $user->email = $userData['email'];
        $user->password = Hash::make($userData['password']);
        $user->save();

        // Asignar el rol de invitado por defecto al usuario
        $this->assignRoleToUser($user, 8);

        return $user;
    }




    public function updateUser(UserUpdateRequest $request, int $id): array
    {
        // Buscar el usuario con el ID dado
        $user = User::findOrFail($id);

        // Actualizar los campos relevantes del usuario con los datos proporcionados
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));
        $user->roles()->detach();
        $this->assignRoleToUser($user,$request->input('role'));

        // Guardar los cambios en la base de datos
        $user->save();

        // Asignar roles u otras operaciones relacionadas con la gestión de usuarios

        return $this->JSONmaker($user);
    }



    public function deleteUser(int $id): void
    {

        $user = User::findOrFail($id);
        $user->roles()->detach();
        $user->delete();
    }




    public function getAllUsers($perPage = 10):LengthAwarePaginator
    {
        $usersPaginator = User::with('roles:id')->paginate($perPage);

        $users = collect($usersPaginator->items());

        $formattedUsers = $users->map(function ($user) {
            return $this->JSONmaker($user);
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
    }




    public function getUser(int $userId):  User|null
    {
        try {

            $user = User::with('roles:id')->findOrFail($userId);
    
            return $this->JSONmaker($user);
        } catch (ModelNotFoundException $exception) {
            // El usuario no fue encontrado
            return null;
        }
    }



    private function assignRoleToUser(User $user, int $roleId): void
    {

        $user->assignRole($roleId);
    }



 /*   public function getUserByHint(string $hint):?User
    {

        $queryhint = '%' . urldecode($hint) . '%';

        $user = User::where(function (Builder $query) use ($queryhint) {
            $query->where('name', 'LIKE', $queryhint);
        })->get();
    
        if ($user->isEmpty()) {
            return null;
        }
    
        return $this->getUser($user->first()->id);
       
    }*/   

  


    private function JSONmaker($user): array
    {
        $role = $user->roles->first();

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
            'role' => $role ? $role->id : null,
        ];
    }





}
