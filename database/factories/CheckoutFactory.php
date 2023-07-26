<?php

namespace Database\Factories;

use App\Models\Checkout;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Generator as Faker;

class CheckoutFactory extends Factory
{
    protected $model = Checkout::class;

    public function definition()
    {
        return [
            'user_id' => function () {
                return \App\Models\User::factory()->create()->id;
            },
            'book_id' => function () {
                return \App\Models\Book::factory()->create()->id;
            },
            'checkout_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'return_date' => $this->faker->dateTimeBetween('now', '+1 year'),
        ];
    }
}