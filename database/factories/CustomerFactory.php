<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'job_ref' => 'ETP-' . fake()->unique()->numberBetween(1000, 9999),
            'job_details' => fake()->sentence(),
            'completion_date' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'address' => fake()->address(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'job_notes' => fake()->optional()->paragraph(),
            'status' => fake()->randomElement(['active', 'nocontract', 'expiring', 'new', 'previous', 'updated']),
            'region' => fake()->randomElement(Customer::AREAS),
        ];
    }
}
