<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
$admin = Role::create(['name'=> 'admin']);
$owner = Role::create(['name'=> 'owner']); //propietario de la compañia
$ceo = Role::create(['name'=> 'ceo']); //gerente general
$sectorManager = Role::create(['name'=> 'sectorManager']); //gerente de sector
$depManager = Role::create(['name'=> 'depManager']);//gerente de departamento
$depHead = Role::create(['name'=> 'depHead']);//jefe de departamento
$depWorker = Role::create(['name'=> 'depWorker']);//operario de departamento
$guess = Role::create(['name'=> 'guest']);//invitado


//permission::create(['name'=>'user.index'])->syncRoles([$admin]); //listar los usuarios registrados en el dominio.
//permission::create(['name'=>'user.create'])->syncRoles([$admin]); //crear un usuario en la página.
//permission::create(['name'=>'user.update'])->syncRoles([$admin]); //actualizar los datos de un usuario del dominio.
//permission::create(['name'=>'user.read'])->syncRoles([$admin]); //Buscar los datos por id de un usuario del dominio.
//permission::create(['name'=>'user.delete'])->syncRoles([$admin]); //Borrar los datos de un usuario del dominio.
//permission::create(['name'=>'user.search'])->syncRoles([$admin]); //Buscar los datos por nombre de un usuario del dominio.
//permission::create(['name'=>'user.hintSearch'])->syncRoles([$admin]); //Buscar los datos por pista del nombre de un usuario del dominio.





    }
}
