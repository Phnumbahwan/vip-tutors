<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(['pending', 'completed']),
            'priority' => $this->faker->randomElement(['low', 'medium', 'high']),
            'order' => $this->faker->numberBetween(0, 100),
            'user_id' => User::factory(),
        ];
    }
} 