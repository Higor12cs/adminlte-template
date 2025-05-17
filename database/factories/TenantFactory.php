<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TenantFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'trial_ends_at' => fake()->dateTimeBetween('now', '+1 month'),
            'subscription_ends_at' => fake()->dateTimeBetween('now', '+1 year'),
            'subscription_cancelled_at' => null,
        ];
    }
}
