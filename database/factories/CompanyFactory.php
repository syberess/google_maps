<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\CompanyStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'google_place_id' => 'ChIJ' . fake()->unique()->regexify('[A-Za-z0-9]{27}'),
            'phone' => fake()->phoneNumber(),
            'website' => fake()->optional()->url(),
            'address' => fake()->address(),
            'latitude' => fake()->latitude(36, 42), // Türkiye koordinatları
            'longitude' => fake()->longitude(26, 45),
            'rating' => fake()->randomFloat(1, 1, 5),
            'review_count' => fake()->numberBetween(0, 500),
            'category' => fake()->randomElement(['Restaurant', 'Cafe', 'Hotel', 'Shop', 'Office']),
            'types' => fake()->randomElements(['restaurant', 'food', 'establishment', 'point_of_interest'], 2),
            'source' => 'google_maps',
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Belirli bir statü ile oluştur
     */
    public function withStatus(CompanyStatus $status): static
    {
        return $this->state(fn (array $attributes) => [
            'status_id' => $status->id,
        ]);
    }

    /**
     * Koordinatsız firma
     */
    public function withoutCoordinates(): static
    {
        return $this->state(fn (array $attributes) => [
            'latitude' => null,
            'longitude' => null,
        ]);
    }

    /**
     * Yüksek puanlı firma
     */
    public function highRated(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => fake()->randomFloat(1, 4.5, 5),
            'review_count' => fake()->numberBetween(100, 500),
        ]);
    }
}
