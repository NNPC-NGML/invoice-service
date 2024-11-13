<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="InvoiceAdviceApprovedBy",
 *     type="object",
 *     title="Invoice Advice Approved By",
 *     @OA\Property(property="id", type="integer", example=1, description="ID of the invoice advice approval record"),
 *     @OA\Property(property="user_id", type="integer", example=1, description="ID of the user who approved the invoice advice"),
 *     @OA\Property(property="invoice_advice_id", type="integer", example=1, description="ID of the invoice advice being approved"),
 *     @OA\Property(property="approval_for", type="integer", example=1, description="Approval status code or type"),
 *     @OA\Property(property="date", type="string", format="date-time", example="2024-09-23T10:00:00Z", description="Date of approval"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Created at"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Updated at"),
 * )
 */
class InvoiceAdviceApprovedByResource extends JsonResource
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
            'user_id' => $this->user_id,
            'invoice_advice_id' => $this->invoice_advice_id,
            'approval_for' => $this->approval_for,
            'date' => $this->date->toDateTimeString(),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString()
        ];
    }
}
