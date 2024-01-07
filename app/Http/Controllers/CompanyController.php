<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CompanyRequests\CompanyCreateRequest;
use App\Services\User\AuthService;
use App\Services\User\UserService;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use App\Models\User;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //Only available to admins.
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CompanyCreateRequest $request)
    {
        //The user who creates the company must be role re assigned to owner.
        $user = Auth::guard('sanctum')->user();
        $this->becomeOwner($user);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //Only available to staff members.
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //Only available to admins.
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //The user who creates the company can destroy it. But after that, it will be removed to
        //a new table called deleted_companies.
    }

    private function becomeOwner(User $user)
    {

        $authService = new AuthService();
        $userService = new UserService($user, $authService);
        $ownerRole = Role::where('name', 'owner')->first();
        $userService->$this->assignUserRole($user, $ownerRole);

    }
}
