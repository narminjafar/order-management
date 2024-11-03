<?php

namespace App\Services\Order;

use App\Exceptions\OrderModificationException;
use App\Interfaces\Repository\OrderProductRepositoryInterface;
use App\Interfaces\Repository\OrderRepositoryInterface;
use App\Interfaces\Repository\ProductRepositoryInterface;
use App\Services\BaseService;
use App\Traits\DBTransaction;
use App\Traits\OrderHelper;
use App\Traits\ProductHelper;
use Exception;
use RuntimeException;

class OrderService extends BaseService
{
    use OrderHelper,ProductHelper,DBTransaction;

    public function __construct(
        protected OrderRepositoryInterface        $orderRepository,
        protected OrderProductRepositoryInterface $orderProductRepository,
        protected ProductRepositoryInterface      $productRepository,
    )
    {
    }

    /**
     * Retrieves orders with an optional status filter and pagination.
     *
     * @param bool|null $status Filter orders by status if provided
     * @param int|null $page Pagination page number
     * @return mixed Paginated list of orders
     */

    public function getOrders(?bool $status = null, ?int $page = null)
    {
        return $this->getOrdersWithFilters(['status' => $status], $page);
    }

    /**
     * Retrieves orders for a specific user, with optional status and pagination.
     *
     * @param int $userId ID of the user
     * @param bool|null $status Filter orders by status if provided
     * @param int|null $page Pagination page number
     * @return mixed Paginated list of user-specific orders
     */

    public function getOrdersByUser(int $userId, ?bool $status = null, ?int $page = null)
    {
        return $this->getOrdersWithFilters(['user_id' => $userId, 'status' => $status], $page);
    }

    /**
     * Adds a new order for a user, with associated products.
     *
     * @param int $userId ID of the user creating the order
     * @param array $data Data containing products and quantities
     * @return mixed Created order with order details
     * @throws RuntimeException if order creation fails
     */

    public function addOrder(int $userId, array $data,$discountRate = null, $taxRate = null)
    {
        return $this->runInTransaction(function () use ($userId, $data) {

            $order = $this->orderRepository->create(['user_id' => $userId]);
            $order->order_number = 'ORD-' . $order->id;
            $order->save();

            $this->addOrUpdateProductsToOrder($order, $data['products'] ?? [], $data['discountRate'] ?? null, $data['taxRate'] ?? null);

            $order->save();

            return $order;

        }, 'Order creation failed.',500);
    }

    /**
     * Retrieves a specific order by ID.
     *
     * @param int $orderId ID of the order to retrieve
     * @return mixed The order data
     * @throws Exception if the order is not found
     */

    public function getOrderById(int $orderId)
    {
        return $this->findOrFail($orderId);
    }

    /**
     * Updates an order's products and quantities.
     *
     * @param int $orderId ID of the order to update
     * @param array $data Updated data, including product IDs and quantities
     * @return mixed Updated order data
     * @throws RuntimeException if update fails
     */

    public function updateOrder(int $orderId, array $data)
    {
        return $this->runInTransaction(function () use ($orderId, $data) {

            $order = $this->findOrFail($orderId, ['orderProducts']);
            $this->validateOrderForUpdate($order);

            $existingProductIds = $order->orderProducts->pluck('product_id')->toArray();

            $this->addOrUpdateProductsToOrder($order, $data['products'] ?? []);

            $newProductIds = array_column($data['products'] ?? [], 'id');
            $this->removeProductsFromOrder($order->id, $existingProductIds, $newProductIds);

            $order->save();

            return $order;

        }, 'Order update failed: ',500);
    }

    /**
     * Deletes an order if it meets the deletion criteria.
     *
     * @param int $orderId ID of the order to delete
     * @return bool True if the order was successfully deleted
     * @throws OrderModificationException if order cannot be deleted
     */

    public function deleteOrder(int $orderId): bool
    {
        $order = $this->findOrFail($orderId);
        $this->validateOrderForDeletion($order);

        return (bool)$this->orderRepository->delete($orderId);
    }



}
