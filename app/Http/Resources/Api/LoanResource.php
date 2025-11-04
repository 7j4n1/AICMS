<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoanResource extends JsonResource
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
            'loanDate' => $this->loanDate,
            'repaymentDate' => $this->repaymentDate,
            'guarantor1' => $this->guarantor1,
            'guarantor2' => $this->guarantor2,
            'guarantor3' => $this->guarantor3,
            'guarantor4' => $this->guarantor4,
            'status' => $this->status,
            'member' => $this->whenLoaded('member'),
            'activeLoan' => $this->whenLoaded('activeLoan'),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
