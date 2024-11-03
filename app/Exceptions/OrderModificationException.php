<?php

namespace App\Exceptions;

use Exception;

class OrderModificationException extends Exception
{
    protected $message = 'Order is already marked as paid and cannot be modified.';

    public function __construct(int $orderId, string $message = null)
    {
        parent::__construct("{$message} Order ID: {$orderId}",400);
    }
}
