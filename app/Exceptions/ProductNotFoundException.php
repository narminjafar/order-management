<?php

namespace App\Exceptions;

use Exception;

class ProductNotFoundException extends Exception
{
    protected $message = 'Product not found.';

    public function __construct($productId)
    {
        parent::__construct("Product with ID {$productId} not found.");
    }
}
