<?php

namespace App\Traits;

use App\Exceptions\OrderModificationException;
use App\Exceptions\OrderNotFoundException;
use App\Services\Order\Discounts\FixedRateDiscount;
use App\Services\Order\Taxes\PercentageTax;

trait OrderHelper
{
    /**
     * Retrieves orders with optional filters and pagination.
     *
     * @param array $filters Filters to apply when retrieving orders.
     * @param int|null $page Pagination page number; defaults to the first page if null.
     * @return mixed Paginated list of orders.
     */

    protected function getOrdersWithFilters(array $filters, ?int $page = null)
    {
        $filters = array_filter($filters, function($value) {
            return !is_null($value) && $value !== '';
        });

        $perPage = 10;

        return $this->orderRepository->all($filters, $perPage, $page);
    }


    /**
     * Adds or updates products within an order.
     *
     * @param mixed $order The order to which products are being added/updated
     * @param array $products Array of product data with quantities
     */

    protected function addOrUpdateProductsToOrder($order, array $products, $discountRate = null, $taxRate = null)
    {
        $totalPrice = 0;
        $totalQuantity = 0;

        foreach ($products as $product) {
            $productModel = $this->findProductOrFail($product['id']);

            $productPrice = $productModel->price;
            $quantity = $product['quantity'];
            $availableStock = $productModel->stock;

            $this->checkStockAvailability($product['id'],$availableStock, $quantity);

            $totalPrice += $productPrice * $quantity;
            $totalQuantity += $quantity;

            $this->orderProductRepository->updateOrCreate(
                ['order_id' => $order->id, 'product_id' => $product['id']],
                ['quantity' => $quantity, 'total_price' => $productPrice * $quantity]
            );
        }

        $discountStrategy = new FixedRateDiscount($discountRate);
        $taxStrategy = new PercentageTax($taxRate);

        $discountAmount = $discountStrategy->calculateDiscount($totalPrice);
        $totalPriceAfterDiscount = $totalPrice - $discountAmount;

        $taxAmount = $taxStrategy->calculateTax($totalPriceAfterDiscount);
        $finalTotalPrice = $totalPriceAfterDiscount + $taxAmount;

        $order->total_price = $finalTotalPrice;
        $order->quantity = $totalQuantity;

    }

    /**
     * Removes products from an order that are no longer in the updated product list.
     *
     * @param int $orderId ID of the order
     * @param array $existingProductIds List of current product IDs in the order
     * @param array $newProductIds List of updated product IDs to remain in the order
     */

    protected function removeProductsFromOrder(int $orderId, array $existingProductIds, array $newProductIds)
    {
        $deletedProductIds = array_diff($existingProductIds, $newProductIds);

        foreach ($deletedProductIds as $productId) {
            $this->orderProductRepository->deleteByOrderIdAndProductId($orderId, $productId);
        }
    }

    /**
     * Finds an order by ID or throws an exception if not found.
     *
     * @param int $orderId ID of the order
     * @param array $relations Related models to load with the order
     * @return mixed Order data
     * @throws OrderNotFoundException if order is not found
     */

    protected function findOrFail(int $orderId, array $relations = [])
    {
        $order = $this->orderRepository->find($orderId, $relations);
        if (!$order) {
            throw new OrderNotFoundException($orderId);
        }
        return $order;
    }

    /**
     * Validates if the order can be updated.
     *
     * @param mixed $order Order object
     * @throws OrderModificationException if the order cannot be modified
     */

    protected function validateOrderForUpdate($order)
    {
        if ($order->paid == 1) {
            throw new OrderModificationException($order->id);
        }
    }

    /**
     * Validates if the order can be deleted.
     *
     * @param mixed $order Order object
     * @throws OrderModificationException if the order cannot be deleted
     */

    protected function validateOrderForDeletion($order)
    {
        if ($order->paid == 1) {
            throw new OrderModificationException($order->id, 'Order is already marked as paid and cannot be deleted.');
        }
    }

}
