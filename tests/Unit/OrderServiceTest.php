<?php

namespace Tests\Unit;

use App\Exceptions\OrderNotFoundException;
use App\Exceptions\UnauthorizedException;
use App\Interfaces\Repository\OrderProductRepositoryInterface;
use App\Interfaces\Repository\OrderRepositoryInterface;
use App\Interfaces\Repository\ProductRepositoryInterface;
use App\Services\Order\OrderService;
use Illuminate\Database\Eloquent\Model;
use Mockery;
use RuntimeException;
use Tests\TestCase;
use TypeError;

class OrderServiceTest extends TestCase
{
    protected OrderService $orderService;
    protected $orderRepository;
    protected $orderProductRepository;
    protected $productRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->orderRepository = Mockery::mock(OrderRepositoryInterface::class);
        $this->orderProductRepository = Mockery::mock(OrderProductRepositoryInterface::class);
        $this->productRepository = Mockery::mock(ProductRepositoryInterface::class);

        $this->orderService = new OrderService(
            $this->orderRepository,
            $this->orderProductRepository,
            $this->productRepository
        );
    }

    /**
     * @group create
     */

    public function testAddOrder()
    {
        $userId = 1;
        $orderData = [
            'products' => [
                ['id' => 1, 'quantity' => 2],
                ['id' => 2, 'quantity' => 1],
            ]
        ];

        $mockOrder = Mockery::mock(Model::class);
        $mockOrder->shouldReceive('save')->andReturn(true);
        $mockOrder->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $mockOrder->shouldReceive('getAttribute')->with('order_number')->andReturn('ORD-1');
        $mockOrder->shouldReceive('setAttribute')->with('order_number', 'ORD-1')->andReturn(null);
        $mockOrder->shouldReceive('setAttribute')->with('total_price', 400)->andReturn(null);
        $mockOrder->shouldReceive('setAttribute')->with('quantity', 3)->andReturn(null);

        $this->orderRepository->shouldReceive('create')
            ->with(['user_id' => $userId])
            ->andReturn($mockOrder);

        $mockProduct1 = Mockery::mock(Model::class);
        $mockProduct1->shouldReceive('getAttribute')->with('price')->andReturn(100);

        $mockProduct2 = Mockery::mock(Model::class);
        $mockProduct2->shouldReceive('getAttribute')->with('price')->andReturn(200);

        $this->productRepository->shouldReceive('find')
            ->with(1)
            ->andReturn($mockProduct1);

        $this->productRepository->shouldReceive('find')
            ->with(2)
            ->andReturn($mockProduct2);

        $this->orderProductRepository->shouldReceive('updateOrCreate')
            ->with(
                ['order_id' => 1, 'product_id' => 1],
                ['quantity' => 2, 'total_price' => 200]
            )
            ->andReturn(Mockery::mock(Model::class));

        $this->orderProductRepository->shouldReceive('updateOrCreate')
            ->with(
                ['order_id' => 1, 'product_id' => 2],
                ['quantity' => 1, 'total_price' => 200]
            )
            ->andReturn(Mockery::mock(Model::class));

        $result = $this->orderService->addOrder($userId, $orderData);

        $this->assertNotNull($result);
        $this->assertEquals('ORD-1', $result->order_number);
    }

    /**
     * @group create
     */

    public function testAddOrderThrowsException()
    {
        $this->expectException(RuntimeException::class);

        $this->orderService->addOrder(1, []);
    }

    /**
     * @group show
     */

    public function testShowThrowsNotFoundExceptionForInvalidId()
    {
        $this->orderRepository->shouldReceive('find')
            ->with(999,[])
            ->andReturn(null);

        $this->expectException(OrderNotFoundException::class);
        $this->expectExceptionMessage("Order with ID 999 not found.");

        $this->orderService->getOrderById(999);
    }

    /**
     * @group show
     */

    public function testShowThrowsTypeErrorForInvalidIdType()
    {
        $this->expectException(TypeError::class);
        $this->orderService->getOrderById('invalid');
    }


    /**
     * @group show
     */

    public function testShowThrowsDatabaseExceptionOnConnectionError()
    {
        $this->orderRepository->shouldReceive('find')
            ->with(999,[])
            ->andThrow(new \Illuminate\Database\QueryException("Connection error",'sql', [], new \Exception));

        $this->expectException(\Illuminate\Database\QueryException::class);

        $this->orderService->getOrderById(999);
    }


    /**
     * @group show
     */

    public function testShowThrowsUnauthorizedExceptionIfNotAuthorized()
    {
        $this->expectException(UnauthorizedException::class);

        $this->orderRepository->shouldReceive('find')
            ->with(999,[])
            ->andThrow(new UnauthorizedException());

        $this->orderService->getOrderById(999);
    }



}


