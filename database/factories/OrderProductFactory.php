<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderProduct>
 */
class OrderProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
               'order_id' => $this->faker->randomElement(Order::pluck('id')->toArray()),
               'product_id' =>$this->faker->randomElement(Product::pluck('id')->toArray()),
               'quantity' => $this->faker->numberBetween(1, 5),
               'total_price' => $this->faker->randomFloat(2, 50, 1000)
        ];
    }
}
