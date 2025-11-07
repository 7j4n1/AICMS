<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemCaptureResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'coopId' => $this->coopId,
            'category_id' => $this->category_id,
            'category' => $this->whenLoaded('category'),
            'quantity' => $this->quantity,
            'price' => $this->price,
            'description' => $this->description,
            'buyingDate' => $this->buyingDate,
            'repaymentDate' => $this->repaymentDate,
            'payment_timeframe' => $this->payment_timeframe,
            'payment_status' => $this->payment_status,
            'loanPaid' => $this->loanPaid,
            'loanBalance' => $this->loanBalance,
            'member' => $this->whenLoaded('member'),
            'repayments' => $this->whenLoaded('repayments'),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
