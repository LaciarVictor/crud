<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

/**
 * Esta clase se encarga de controlar los roles de los usuarios
 */
class RoleController extends Controller
{

    /**
     * Trata de listar todos los roles
     *
     * @return void
     */
    public function index()
    {
        $roles = Role::all();
        return response()->json($roles);
    }



    /**
     * Trata de guardar un rol
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        //
    }



    /**
     * Trata de mostrar un rol por su id
     *
     * @param string $id
     * @return void
     */
    public function show(string $id)
    {
       $roles = Role::orderBy('id')->pluck('name');
       return response()->json($roles);
    }



    /**
     * Trata de actualizar un rol
     *
     * @param Request $request
     * @param string $id
     * @return void
     */
    public function update(Request $request, string $id)
    {
        //
    }

 

    /**
     * Trata de eliminar un rol
     *
     * @param string $id
     * @return void
     */
    public function destroy(string $id)
    {
        //
    }
}