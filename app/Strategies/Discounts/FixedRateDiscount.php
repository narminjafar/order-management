<?php

namespace App\Strategies\Discounts;
use App\Interfaces\Strategy\DiscountStrategy;

class FixedRateDiscount implements DiscountStrategy
{
    protected $discountRate;

    public function __construct($discountRate)
    {
        $this->discountRate = $discountRate;
    }

    public function calculateDiscount($totalPrice): float
    {
        return $totalPrice * ($this->discountRate / 100);
    }
}
