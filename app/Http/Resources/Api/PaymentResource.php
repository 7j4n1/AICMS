<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
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
            'loanAmount' => $this->loanAmount,
            'savingAmount' => $this->savingAmount,
            'shareAmount' => $this->shareAmount,
            'others' => $this->others,
            'adminCharge' => $this->adminCharge,
            'totalAmount' => $this->totalAmount,
            'paymentDate' => $this->paymentDate,
            'splitOption' => $this->splitOption,
            'otherSavingsType' => $this->otherSavingsType,
            'member' => $this->whenLoaded('member'),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
