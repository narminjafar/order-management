<?php

namespace App\Traits;

use App\Exceptions\InsufficientStockException;
use App\Exceptions\OrderModificationException;
use App\Exceptions\OrderNotFoundException;
use App\Exceptions\ProductNotFoundException;
use Exception;
use Illuminate\Support\Facades\DB;
use RuntimeException;

trait ProductHelper
{
    /**
     * Finds a product by ID or throws an exception if not found.
     *
     * @param int $productId ID of the product to find
     * @return mixed The product data
     * @throws ProductNotFoundException if product is not found
     */

    protected function findProductOrFail(int $productId)
    {
        $product = $this->productRepository->find($productId);
        if (!$product) {
            throw new ProductNotFoundException();
        }
        return $product;
    }

    /**
     * @param int $productId The ID of the product to check stock for.
     * @param int $availableStock The amount of stock available for the product.
     * @param int $quantity The quantity of the product that the user wants to order.
     * @throws \App\Exceptions\InsufficientStockException If the requested quantity is greater than available stock.
     * @return bool Returns true if the stock is sufficient.
     */

    protected function checkStockAvailability($productId, $availableStock, $quantity)
    {
        if ($quantity > $availableStock) {
            throw new InsufficientStockException($productId,$availableStock,$quantity);
        }

        return true;
    }

}
