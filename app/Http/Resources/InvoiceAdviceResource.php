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
 *     @OA\Property(property="department", type="string", example="Finance", description="Department associated with the invoice advice"),
 *     @OA\Property(property="gcc_created_by_id", type="integer", example=7, description="ID of the GCC creator"),
 *     @OA\Property(property="invoice_advice_created_by_id", type="integer", example=8, description="ID of the invoice advice creator"),
 *     @OA\Property(property="from_date", type="string", format="date", example="2024-09-01", description="Start date of the invoice period"),
 *     @OA\Property(property="to_date", type="string", format="date", example="2024-09-30", description="End date of the invoice period"),
 *     @OA\Property(property="total_quantity_of_gas", type="number", format="float", example=12345.67, description="Total quantity of gas"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-08-23T10:00:00Z", description="Created at timestamp"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-09-23T10:00:00Z", description="Updated at timestamp"),
 *     @OA\Property(property="gcc_created_by", ref="#/components/schemas/User", description="Details of the GCC creator"),
 *     @OA\Property(property="invoice_advice_created_by", ref="#/components/schemas/User", description="Details of the invoice advice creator"),
 *     @OA\Property(property="customer", ref="#/components/schemas/Customer", description="Details of the customer"),
 *     @OA\Property(property="customer_site", ref="#/components/schemas/CustomerSite", description="Details of the customer site"),
 *     @OA\Property(
 *         property="invoice_advice_list_items",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/InvoiceAdviceListItem"),
 *         description="List of invoice advice items"
 *     ),
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
            'department' => $this->department,
            'gcc_created_by_id' => $this->gcc_created_by_id,
            'invoice_advice_created_by_id' => $this->gcc_created_by_id,
            'from_date' => $this->from_date?->toDateString(),
            'to_date' => $this->to_date?->toDateString(),
            'total_quantity_of_gas' => $this->total_quantity_of_gas,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            'gcc_created_by' => new UserResource($this->whenLoaded('gcc_created_by')),
            'invoice_advice_created_by' => new UserResource($this->whenLoaded('invoice_advice_created_by')),
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'customer_site' => new CustomerSiteResource($this->whenLoaded('customer_site')),
            'invoice_advice_list_items' => InvoiceAdviceListItemResource::collection($this->whenLoaded('invoice_advice_list_items')),
        ];
    }
}
