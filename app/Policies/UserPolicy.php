<?php


/*
Registra la política en el archivo "app/Providers/AuthServiceProvider.php" agregando la siguiente línea al método boot:
php
Copiar
use App\Models\User;
use App\Policies\UserPolicy;

public function boot()
{
    $this->registerPolicies();

    Gate::resource('users', UserPolicy::class);
}

*/


namespace App\Policies;

use App\Models\User;
use \Illuminate\Support\Facades\Log;


class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function index(User $user): bool
    {
        
        return true;
    }


    /**
     * Determine whether the user can create models.
     */
    public function store(User $user): bool
    {
        return $user->hasRole('admin');
    }
    public function register(User $user): bool
    {
        return true;
    }


    /**
     * Determine whether the user can view the model.
     */
    public function show(User $user): bool
    {
        //Ya que la tabla usuarios sólo es accesible por autenticación
        //cualquier rol puede ver los usuarios.
        return true;
        
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function destroy(User $user): bool
    {
        return $user->hasRole('admin');
    }



}
