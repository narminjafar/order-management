<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
//        return parent::toArray($request);

        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'total_price' => $this->total_price,
            'quantity' => $this->quantity,
            'status' => $this->status,
            'is_paid' => $this->paid,
            'user' =>  new UserResource($this->whenLoaded('user')),
            'products' => ProductResource::collection($this->whenLoaded('products')),
            'created_at' => $this->created_at->toDateTimeString(),
        ];

    }
}
