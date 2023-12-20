<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => "LaciarVictor",
            'email' => "victor@example.com",
            'password' => Hash::make('123456')

        ])->assignRole('admin');

        User::create([
            'name' => "decxx1",
            'email' => "dami@example.com",
            'password' => Hash::make('123456')

        ])->assignRole('admin');

    }
}
