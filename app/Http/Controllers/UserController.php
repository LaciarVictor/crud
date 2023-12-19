<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Services\RegistrationService;


class UserController extends Controller
{


    protected $registrationService;

    public function __construct(RegistrationService $registrationService)
    {

        $this->registrationService = $registrationService;
        
    }




    /**
     * Muestra la lista de usuarios.
     */
    public function index()
    {
        try {

            return $this->getUsersWithRoles();
        } catch (\Exception $e) {

            return response()->json(['message' => 'Error al recuperar usuarios', 
            'error' => $e->getMessage(),], 500);
        }
    }




    /**
     * Guarda un nuevo usuario en la base de datos.
     */
    public function store(UserRequest $request)
    {
        try {

            $user = $this->registrationService->createUser($request);

            

            $this->registrationService->assignRoleToUser($user, $request);

            $user->save();


            return response()->json(['message' => 'Usuario creado con éxito.', 'user' => $this->getUsersWithRoles($user->id),], 200);
        } catch (\Exception $e) {

            return response()->json(['message' => 'Error creando usuario', 'error' => $e->getMessage(),], 500);
        }
    }




    /**
     * Devuelve un usuario determinado por su número de id.
     */
    public function show(string $id)
    {
        try {

            return $this->getUsersWithRoles($id);
        } catch (ModelNotFoundException $e) {

            return response()->json(['message' => 'Usuario no encontrado.',], 404);
        }
    }




    /**
     * Actualiza un usuario específico en la base de datos.
     */
    public function update(UserRequest $request, string $id)
    {
        try {
            //buscar usuario
            $user = User::findOrFail($id);

            //Asignarle los nuevos campos
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->password = Hash::make($request->input('password'));
            $this->registrationService->assignRoleToUser($user, $request->input('role'));
            $user->save();

            //Devolver el JSON con el usuario actualizado.
            return response()->json(['message' => 'Usuario actualizado con éxito.', 'user' => $this->getUsersWithRoles($user->id),], 200);
        } catch (ModelNotFoundException $e) {

            return response()->json(['message' => 'Usuario no encontrado.',], 404);
        }
    }




    /**
     * Remueve un usuario específico de la base de datos.
     */
    public function destroy(string $id)
    {
        try {
            $user = User::findOrFail($id);

            // Eliminar la relación del usuario con el rol
            $user->roles()->detach();

            $user->delete();

            return response()->json(['message' => 'Usuario borrado correctamente.',], 200);
        } catch (ModelNotFoundException $e) {

            return response()->json(['message' => 'Usuario no encontrado.',], 404);
        } catch (\Exception $e) {

            return response()->json(['message' => 'Error borrando usuario.', 'error' => $e->getMessage(),], 500);
        }
    }





/*
*PRIVATE FUNCTIONS
*/



    private function getUsersWithRoles($userId = null)
    {

        //Si no se recibe un ID
        if ($userId === null) {

            //obtener todos los usuarios
            $users = User::with('roles:id')->get();

            //Formatear y devolver el JSON con la lista de usuarios usando la función JSONmaker.
            return $users->map(function ($user) {
                return $this->JSONmaker($user);
            });
        }
        //Si se recibe un ID...
        else {

            //buscar el usuario por su id
            $user = User::with('roles:id')->find($userId);

            // Existe el usuario?
            if (!$user) {
                throw new ModelNotFoundException();
            } else {

                //Formatear y devolver el JSON con el usuario usando la función JSONmaker.
                return $this->JSONmaker($user);
            }
        }
    }




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



    private function hintSearch(string $hint)
    {
        try {
            $user = User::where('name', 'LIKE', '%' . urldecode($hint) . '%')->get();

            return response()->json($this->getUsersWithRoles($user->id));
        } catch (ModelNotFoundException $e) {

            return response()->json(['message' => 'Usuario no encontrado.',], 404);
        } catch (\Exception $e) {

            return response()->json(['message' => 'Error buscando usuario.', 'error' => $e->getMessage(),], 500);
        }
    }
}
