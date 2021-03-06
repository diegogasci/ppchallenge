<?php

namespace Database\Factories;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'email_verified_at' => Carbon::now()->toDateTimeString(),
            'document_type' => 0,
            'document' => rand(00000000001, 99999999999),
            'password' => Hash::make(123456),
            'remember_token' => Str::random(10),
        ];
    }

    public function shopkeeper()
    {
        return $this->state(function (array $attributes) {
            return [
                'document_type' => 1,
            ];
        });
    }
}
