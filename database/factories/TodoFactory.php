<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Todo>
 */
class TodoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'text' => $this->faker->realTextBetween(),
        ];
    }

    public function done(): TodoFactory
    {
        return $this->state(function (array $attribute) {
            return [
                'done' => 1,
            ];
        });
    }

    public function notDone(): TodoFactory
    {
        return $this->state(function (array $attribute) {
            return [
                'done' => 0,
            ];
        });
    }
}
