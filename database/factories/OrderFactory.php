<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $orderNumber = 'ORD-' . strtoupper($this->faker->unique()->randomNumber(5));

        return [
            'order_number' => $orderNumber,
//            'user_id' => $this->faker->randomElement(User::pluck('id')->toArray()),
            'user_id' => 1,
            'quantity' => $this->faker->numberBetween(1, 5),
            'total_price' => $this->faker->randomFloat(2, 50, 1000),
            'status'=> $this->faker->boolean(),
            'paid'=> $this->faker->boolean()
        ];
    }
}
