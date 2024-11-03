<?php

namespace App\Interfaces\Strategy;

interface TaxStrategy
{
    public function calculateTax($totalPriceAfterDiscount): float;
}
