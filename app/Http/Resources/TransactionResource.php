<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'quantity' => $this->quantity,
            'total_amount' => $this->total_amount,
            'purchase_date' => $this->created_at,
            'product' => [
                'id' => $this->product->id,
                'name' => $this->product->name,
                'remaining_quantity' => $this->product->quantity_available
            ]
        ];
    }
}
