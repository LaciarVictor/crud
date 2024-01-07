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
            'user_name' => $this->faker->unique()->userName(),
            'first_name' => $this->faker->unique()->name(),
            'last_name' => $this->faker->unique()->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone_code' => strval($this->faker->numberBetween(1, 399)),
            'phone_number' => strval($this->faker->numberBetween(100000, 999999999)),
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
    // Obtener todos los roles existentes una sola vez
    $roles = Role::all();

    return $this->afterCreating(function ($user) use ($roles) {
        $randomRole = $roles->random(); // Obtener un rol aleatorio
        $user->assignRole($randomRole);
    });
    }
}