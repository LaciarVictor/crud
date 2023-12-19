<?php

namespace App\Services;

use App\Models\User;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Hash;

class RegistrationService
{

    
    public function createUser(UserRequest $request): User
    {
        return User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);
    }



    public function assignRoleToUser(User $user, UserRequest $request): Void
    {
        $roleId = $request->input('role');
        $role = $user->roles->first();

        //Si el usuario tiene un rol asignado, desechar la asignación.
        if ($role !== null) {
            $user->roles()->detach();
        }


        //Reasignar nuevo rol.
        $user->roles()->sync([$roleId]);
    }
}