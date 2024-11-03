<?php

namespace App\Interfaces\Repository;

interface OrderProductRepositoryInterface
{
    public function updateOrCreate(array $filters = [], array $data = []);
    public function deleteByOrderIdAndProductId(int $orderId, int $productId);
}
