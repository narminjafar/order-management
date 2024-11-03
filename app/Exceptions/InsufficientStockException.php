<?php

namespace App\Exceptions;

use Exception;

class InsufficientStockException extends Exception
{
    protected $message = 'Product is not found.';

    public function __construct($productId,$availableStock,$requestedQuantity)
    {
        parent::__construct("Insufficient stock for product ID: {$productId}. Available: {$availableStock}, Requested: {$requestedQuantity}");
    }
}
