<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'userName',
        'firstName',
        'lastName',
        'email',
        'password',
        'phoneCode',
        'phoneNumber',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    protected static function boot()
    {
        parent::boot();


        /**
         * Esta función procesa el request JSON antes de guardarlo en la base de datos.
         * El evento 'creating' se dispara antes de crear un nuevo registro.
         * Dentro del evento 'creating' se puede modificar el modelo antes de ser guardado.
         * El rol no pertenece al modelo User en la base de datos.
         * 
         * @param  \App\Models\User  $user
         */
        static::creating(function ($user) {

            //Si la solicitud JSON tiene el campo password...
            if (!empty($user->password)) {

                // Encriptar el password y asignarlo al usuario.
                $user->password = Hash::make($user->password);
            }

            $role = Role::where('name', $user->role)->first();

            // Si el campo 'role' no está presente o no es válido, asigna el rol 'guest' por defecto.
            if (empty($user->role) || empty($role)) {

                $role = Role::where('name', 'guest')->first();
            }

            // Eliminar el campo rol del objeto JSON
            if (!empty($user->role)) {
                unset($user->role);
            }

            $user->assignRole($role);
        });

        /**
         * Esta función procesa el request JSON antes de guardarlo en la base de datos.
         * El evento 'filling' de eloquent se dispara antes de sobreescribir un registro.
         * Dentro del evento 'filling' se puede modificar el modelo antes de ser guardado.
         * El rol no pertenece al modelo User en la base de datos.
         * 
         * @param  \App\Models\User  $user
         */
        static::updating(function ($user) {

            //Si la solicitud JSON tiene el campo password...
            if (!empty($user->password)) {

                // Encriptar el password y asignarlo al usuario.
                $user->password = Hash::make($user->password);
            }

            $role = Role::where('name', $user->role)->first();

            // Si el campo 'role' no está presente o no es válido, asigna el rol 'guest' por defecto.
            if (empty($user->role) || empty($role)) {

                $role = Role::where('name', 'guest')->first();
            }

            // Eliminar el campo rol del objeto JSON
            if (!empty($user->role)) {
                unset($user->role);
            }

            //eliminar el rol asociado anteriormente al usuario
            $user->roles()->detach();
            $user->assignRole($role);
        });

        static::deleting(function ($user) {
            // Realiza las acciones necesarias antes de eliminar el usuario
            // Por ejemplo, puedes desasignar roles, eliminar relaciones, etc.
           $user->roles()->detach();
           $user->tokens()->delete();
        });
    }
}
