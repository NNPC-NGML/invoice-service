<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="InvoiceAdvice",
 *     type="object",
 *     title="Invoice Advice",
 *     @OA\Property(property="id", type="integer", example=1, description="ID of the invoice advice record"),
 *     @OA\Property(property="with_vat", type="boolean", example=true, description="Indicates if VAT is included"),
 *     @OA\Property(property="customer_id", type="integer", example=123, description="ID of the customer"),
 *     @OA\Property(property="customer_site_id", type="integer", example=456, description="ID of the customer site"),
 *     @OA\Property(property="capex_recovery_amount", type="string", example="10000.00", description="CAPEX recovery amount"),
 *     @OA\Property(property="date", type="string", format="date-time", example="2024-09-23T10:00:00Z", description="Date of the invoice advice"),
 *     @OA\Property(property="status", type="integer", example=1, description="Status of the invoice advice record"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Created at"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Updated at"),
 * )
 */
class InvoiceAdviceResource extends JsonResource
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
            'with_vat' => $this->with_vat,
            'customer_id' => $this->customer_id,
            'customer_site_id' => $this->customer_site_id,
            'capex_recovery_amount' => $this->capex_recovery_amount,
            'date' => $this->date->toDateTimeString(),
            'status' => $this->status,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
