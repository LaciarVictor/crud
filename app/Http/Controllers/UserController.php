<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\FlareClient\Api;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Spatie\Permission\Models\Role;




class UserController extends Controller
{




    //Lista todos los usuarios registrados en el dominio.
    public function index()
    {
        try {

            $users = User::select('id', 'name', 'email', 'created_at', 'updated_at')
                ->with('roles:name')
                ->get()
                ->map(function ($user) {
                    $user->role = $user->roles[0]->name;
                    unset($user->roles);
                    return $user;
                });


            return response()->json($users);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error retrieving users',
                'error' => $e->getMessage(),
            ], 500);
        }
    }




    //Guarda en la DB un usuario pasado como JSON. El UserRequest evalúa si están todos los campos.
    public function create(UserRequest $request)
    {
        try {
            $user = new User();
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->password =  Hash::make($request->input('password'));
            $user->save();
            $user->roles()->sync(Role::where('name', $request->input('role'))->first());

            return response()->json([
                'message' => 'User created successfully',
                'user' => $user,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }




    //Busca el usuario por su ID
    public function read(string $id)
    {

        try {
            $user = User::findOrFail($id);
            return response()->json($user);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Read error: user id not found.',
            ], 404);
        }
    }




    //Actualiza los datos del usuario
    public function update(UserRequest $request, string $id)
    {
        try {
            $user = User::findOrFail($id);
            //asignar al usuario el nombre, email, password actualizado enviados por el front
            $user->name = $request->input('name');
            $user->email = $request->input('email');


            if ($request->has('password')) {
                $user->password = Hash::make($request->input('password'));
            }

        // Actualizar el rol del usuario si se envía en la solicitud
        if ($request->has('role')) {
            $rol = Role::where('name', $request->input('role'))->first();
            if ($rol) {
                $user->roles()->sync([$rol->id]);
            }
        }




            $user->save();

// Obtener el rol actualizado del usuario
$user->load('roles:name');

// Construir la respuesta JSON
$response = [
    'message' => 'User updated successfully',
    'user' => [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'created_at' => $user->created_at,
        'updated_at' => $user->updated_at,
        'role' => $user->roles[0]->name,
    ],
];

return response()->json($response, 200);




        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }
    }




    //Busca un usuario por su nombre exacto. /search/Victor%20Romero
    public function search(string $nombre)
    {
        try {
            $user = User::where('name', 'LIKE', urldecode($nombre))->first();

            if (!$user) {
                return response()->json([
                    'message' => 'User not found',
                ], 404);
            }

            return response()->json($user);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }




    //busca al usuario por una pista de su nombre.
    public function hintSearch(string $hint)
    {
        try {
            $user = User::where('name', 'LIKE', '%' . urldecode($hint) . '%')->get();

            if (!$user) {
                return response()->json([
                    'message' => 'User not found',
                ], 404);
            }

            return response()->json($user);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }




    //Borra un usuario
    public function delete(string $id)
    {

        try {
            $user = User::findOrFail($id);

            // Eliminar la relación del usuario con el rol
            $user->roles()->detach();

            $user->delete();

            return response()->json([
                'message' => 'User deleted successfully',
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error deleting user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
