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

            'userName' => "laciarvictor",
            'firstName'=> "Victor Eduardo",
            'lastName'=> "Victor Eduardo",
            'email' => "laciarvictor@gmail.com",
            'phoneCode'=> "54",
            'phoneNumber'=> "1144138057",
            'password' => Hash::make('123456')

        ])->assignRole('admin');

        User::create([
            'userName' => "decxx1",
            'firstName'=> "Damián",
            'lastName'=> "Cisternas",
            'email' => "decxx1@gmail.com",
            'phoneCode'=> "54",
            'phoneNumber'=> "2612455960",
            'password' => Hash::make('123456')

        ])->assignRole('admin');

        // Generar 1000 usuarios ficticios utilizando el factory
        User::factory()->count(1000)->create();
    }
}
