<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\FlareClient\Api;
use Illuminate\Database\Eloquent\ModelNotFoundException;




class UserController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $users = User::select('id','name', 'email','created_at','updated_at')->get();

            return response()->json($users);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error retrieving users',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function create(UserRequest $request)
    {
        try {
            $user = new User();
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->password =  Hash::make($request->input('password'));
            $user->save();

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
    /**
     * Display the specified resource.
     */
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


    /**
     * Update the specified resource in storage.
     */
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

            $user->save();

            return response()->json([
                'message' => 'User updated successfully',
                'user' => $user,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }
    }


    public function search(string $nombre)
    {
        try {
            $user = User::where('name', 'LIKE' ,urldecode($nombre))->first();
    
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

    public function hintSearch(string $hint)
    {
        try {
            $user = User::where('name', 'LIKE' ,'%'.urldecode($hint).'%')->get();
    
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





    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        try {
            $user = User::findOrFail($id);
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


