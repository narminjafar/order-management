<?php

namespace App\Repositories;

use App\Interfaces\Repository\OrderProductRepositoryInterface;
use App\Models\OrderProduct;

class OrderProductRepository extends BaseRepository implements OrderProductRepositoryInterface
{
    public function __construct(OrderProduct $model)
    {
        parent::__construct($model);
    }
}
