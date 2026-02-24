<?php

namespace Database\Factories;

use App\Models\CompanyStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CompanyStatus>
 */
class CompanyStatusFactory extends Factory
{
    protected $model = CompanyStatus::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'slug' => fake()->unique()->slug(1),
            'color' => fake()->hexColor(),
            'icon' => fake()->randomElement(['circle', 'check', 'clock', 'star']),
            'order' => fake()->unique()->numberBetween(1, 100),
        ];
    }

    /**
     * Prospekt durumu
     */
    public function prospekt(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Prospekt',
            'slug' => 'prospekt',
            'color' => '#10B981',
            'icon' => 'circle',
            'order' => 1,
        ]);
    }

    /**
     * Müşteri durumu
     */
    public function musteri(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Müşteri',
            'slug' => 'musteri',
            'color' => '#3B82F6',
            'icon' => 'check-circle',
            'order' => 3,
        ]);
    }
}
