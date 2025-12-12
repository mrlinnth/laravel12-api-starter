<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'content' => fake()->paragraphs(3, true),
            'status' => fake()->randomElement(["draft","published","archived"]),
            'user_id' => User::factory(),
            'published_at' => fake()->dateTime(),
        ];
    }
}
