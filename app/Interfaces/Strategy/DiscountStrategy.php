<?php

namespace App\Interfaces\Strategy;

interface DiscountStrategy
{
    public function calculateDiscount($totalPrice): float;
}
