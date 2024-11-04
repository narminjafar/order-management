<?php

namespace App\Http\Controllers;

use App\Http\Requests\Order\OrderListRequest;
use App\Http\Requests\Order\OrderStoreRequest;
use App\Http\Requests\Order\OrderUpdateRequest;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
        $this->middleware('validate.id:order')->only(['show', 'update', 'destroy']);
    }

    /**
     * Retrieves a list of orders based on optional filters for status and pagination.
     *
     * @param OrderListRequest $request The request object containing filter and pagination parameters.
     * @return JsonResponse A JSON response containing the list of orders.
     */

    public function list(OrderListRequest $request): JsonResponse
    {
        $data = $this->orderService->getOrders($request->status, $request->page);
        return $this->successResponse('Orders retrieved successfully', $data);
    }

    /**
     * Retrieves a list of orders for a specific user based on optional filters.
     *
     * @param OrderListRequest $request The request object containing filter and pagination parameters.
     * @return JsonResponse A JSON response containing the list of user-specific orders.
     */

    public function listByUser(OrderListRequest $request): JsonResponse
    {
        $authUser = $request->attributes->get('auth_user');
        $data = $this->orderService->getOrdersByUser($authUser->id, $request->status, $request->page);
        return $this->successResponse('Orders retrieved successfully', $data);
    }

    /**
     * Stores a new order for the authenticated user.
     *
     * @param OrderStoreRequest $request The request object containing the order data.
     * @return JsonResponse A JSON response indicating the result of the order creation.
     */

    public function store(OrderStoreRequest $request): JsonResponse
    {
        $authUser = $request->attributes->get('auth_user');
        return $this->handleResponse(function () use ($authUser, $request) {
            $data = $this->orderService->addOrder($authUser->id, $request->all());
            return $this->result('Order created successfully', $data, 201, OrderResource::class);
        });
    }

    /**
     * Retrieves a specific order by its ID.
     *
     * @param int $id The ID of the order to retrieve.
     * @return JsonResponse A JSON response containing the order details.
     */

    public function show($id)
    {
        return $this->handleResponse(function () use ($id) {
            $data = $this->orderService->getOrderById($id);
            return $this->result('Order retrieved successfully.', $data, 200, OrderResource::class);
        });
    }

    /**
     * Updates an existing order based on the provided ID and data.
     *
     * @param OrderUpdateRequest $request The request object containing the updated order data.
     * @param int $id The ID of the order to update.
     * @return JsonResponse A JSON response indicating the result of the order update.
     */

    public function update(OrderUpdateRequest $request, $id)
    {
        return $this->handleResponse(function () use ($id, $request) {
            $data = $this->orderService->updateOrder($id, $request->all());
            return $this->successResponse('Order updated successfully', $data);
        });
    }


    /**
     * Deletes an existing order by its ID.
     *
     * @param int $id The ID of the order to delete.
     * @return JsonResponse A JSON response indicating the result of the order deletion.
     */

    public function destroy($id)
    {
        return $this->handleResponse(function () use ($id) {
            $this->orderService->deleteOrder($id);
            return $this->successResponse('Order deleted successfully', null);
        });
    }

}
