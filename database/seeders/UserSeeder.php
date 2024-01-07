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

            'user_name' => "laciarvictor",
            'first_name'=> "Victor Eduardo",
            'last_name'=> "Victor Eduardo",
            'email' => "laciarvictor@gmail.com",
            'phone_code'=> "54",
            'phone_number'=> "1144138057",
            'password' => Hash::make('123456')

        ])->assignRole('admin');

        User::create([
            'user_name' => "decxx1",
            'first_name'=> "Damián",
            'last_name'=> "Cisternas",
            'email' => "decxx1@gmail.com",
            'phone_code'=> "54",
            'phone_number'=> "2612455960",
            'password' => Hash::make('123456')

        ])->assignRole('admin');

        // Generar 1000 usuarios ficticios utilizando el factory
        User::factory()->count(1000)->create();
    }
}
