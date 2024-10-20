<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="Invoice",
 *     type="object",
 *     title="Invoice",
 *     @OA\Property(property="id", type="integer", example=1, description="ID of the invoice record"),
 *     @OA\Property(property="invoice_number", type="string", example="INV-12345", description="Unique invoice number"),
 *     @OA\Property(property="invoice_advice_id", type="integer", example=1, description="ID of the related invoice advice"),
 *     @OA\Property(property="consumed_volume_amount_in_naira", type="string", example="1000.00", description="Consumed volume amount in Naira"),
 *     @OA\Property(property="consumed_volume_amount_in_dollar", type="string", example="2.50", description="Consumed volume amount in Dollar"),
 *     @OA\Property(property="dollar_to_naira_convertion_rate", type="string", example="400.00", description="Conversion rate from Dollar to Naira"),
 *     @OA\Property(property="vat_amount", type="string", example="250.00", description="VAT amount for the invoice"),
 *     @OA\Property(property="total_volume_paid_for", type="string", example="1250.00", description="Total volume paid for"),
 *     @OA\Property(property="status", type="integer", example=1, description="Status of the invoice (e.g., 0 = unpaid, 1 = paid)"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Created at"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Updated at"),
 * )
 */
class InvoiceResource extends JsonResource
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
            'invoice_number' => $this->invoice_number,
            'invoice_advice_id' => $this->invoice_advice_id,
            'consumed_volume_amount_in_naira' => $this->consumed_volume_amount_in_naira,
            'consumed_volume_amount_in_dollar' => $this->consumed_volume_amount_in_dollar,
            'dollar_to_naira_convertion_rate' => $this->dollar_to_naira_convertion_rate,
            'vat_amount' => $this->vat_amount,
            'total_volume_paid_for' => $this->total_volume_paid_for,
            'status' => $this->status,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString()
        ];
    }
}
