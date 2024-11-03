<?php

namespace App\Exceptions;

use Exception;

class OrderNotFoundException extends Exception
{
    protected $message = 'Order not found.';

    public function __construct($orderId)
    {
        parent::__construct("Order with ID {$orderId} not found.",404);
    }
}
