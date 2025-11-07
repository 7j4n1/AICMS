<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MemberResource extends JsonResource
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
            'groupId' => $this->groupId,
            'surname' => $this->surname,
            'otherNames' => $this->otherNames,
            'occupation' => $this->occupation,
            'gender' => $this->gender,
            'religion' => $this->religion,
            'phoneNumber' => $this->phoneNumber,
            'bankName' => $this->bankName,
            'accountNumber' => $this->accountNumber,
            'nextOfKinName' => $this->nextOfKinName,
            'nextOfKinPhoneNumber' => $this->nextOfKinPhoneNumber,
            'yearJoined' => $this->yearJoined,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
