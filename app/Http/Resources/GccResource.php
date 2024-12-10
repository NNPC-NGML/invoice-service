<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GccResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "list_item" => $this->listItems,
            "gcc" => parent::toArray($request),
            "invoice_advice" => $this->invoiceAdvice,
            "invoice" => $this->invoice,
        ];
    }
}
