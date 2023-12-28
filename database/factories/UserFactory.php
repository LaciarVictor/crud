<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'userName' => $this->faker->unique()->userName(),
            'firstName' => $this->faker->unique()->name(),
            'lastName' => $this->faker->unique()->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phoneCode' => strval($this->faker->numberBetween(1, 399)),
            'phoneNumber' => strval($this->faker->numberBetween(100000, 999999999)),
            'password' => static::$password ??= Hash::make('password'),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function configure()
    {
        return $this->afterCreating(function ($user) {
            $roles = Role::all(); // Obtener todos los roles existentes
            $randomRole = $roles->random(); // Obtener un rol aleatorio
            $user->assignRole($randomRole);
        });
    }
}