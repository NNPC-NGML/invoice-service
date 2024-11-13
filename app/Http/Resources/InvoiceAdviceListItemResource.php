<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="InvoiceAdviceListItem",
 *     type="object",
 *     title="Invoice Advice List Item",
 *     @OA\Property(property="id", type="integer", example=1, description="ID of the invoice advice list item"),
 *     @OA\Property(property="customer_id", type="integer", example=123, description="ID of the customer"),
 *     @OA\Property(property="customer_site_id", type="integer", example=456, description="ID of the customer site"),
 *     @OA\Property(property="volume", type="string", example="1000", description="Volume of gas or energy"),
 *     @OA\Property(property="inlet", type="string", example="50", description="Inlet value"),
 *     @OA\Property(property="outlet", type="string", example="40", description="Outlet value"),
 *     @OA\Property(property="take_or_pay_value", type="string", example="500", description="Take or Pay value"),
 *     @OA\Property(property="allocation", type="string", example="300", description="Allocation value"),
 *     @OA\Property(property="daily_target", type="string", example="200", description="Daily target value"),
 *     @OA\Property(property="nomination", type="string", example="250", description="Nomination value"),
 *     @OA\Property(property="daily_gas_id", type="integer", example=789, description="ID of the associated daily gas record"),
 *     @OA\Property(property="date", type="string", format="date-time", example="2024-09-23T10:00:00Z", description="Date of the record"),
 *     @OA\Property(property="status", type="integer", example=1, description="Status of the invoice advice list item"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Created at"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Updated at"),
 * )
 */
class InvoiceAdviceListItemResource extends JsonResource
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
            'customer_id' => $this->customer_id,
            'customer_site_id' => $this->customer_site_id,
            'volume' => $this->volume,
            'inlet' => $this->inlet,
            'outlet' => $this->outlet,
            'take_or_pay_value' => $this->take_or_pay_value,
            'allocation' => $this->allocation,
            'daily_target' => $this->daily_target,
            'nomination' => $this->nomination,
            'daily_gas_id' => $this->daily_gas_id,
            'date' => $this->date->toDateTimeString(),
            'status' => $this->status,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
