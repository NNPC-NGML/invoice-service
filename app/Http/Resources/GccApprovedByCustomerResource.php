<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="GccApprovedByCustomer",
 *     type="object",
 *     title="GCC Approved by Customer",
 *     @OA\Property(property="id", type="integer", example=1, description="ID of the approved record"),
 *     @OA\Property(property="customer_name", type="string", example="John Doe", description="Name of the customer"),
 *     @OA\Property(property="signature", type="string", example="base64_encoded_signature", description="Signature of the customer"),
 *     @OA\Property(property="date", type="string", format="date-time", example="2024-09-23T14:30:00Z", description="Date of approval"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Created at"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Updated at"),
 * )
 */
class GccApprovedByCustomerResource extends JsonResource
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
            'customer_name' => $this->customer_name,
            'signature' => $this->signature,
            'date' => $this->date->toDateTimeString(),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString()
        ];
    }
}
