<?php

// app/Services/Taxes/PercentageTax.php
namespace App\Strategies\Taxes;
use App\Interfaces\Strategy\TaxStrategy;

class PercentageTax implements TaxStrategy
{
protected $taxRate;

public function __construct($taxRate)
{
$this->taxRate = $taxRate;
}

public function calculateTax($totalPriceAfterDiscount): float
{
return $totalPriceAfterDiscount * ($this->taxRate / 100);
}
}
