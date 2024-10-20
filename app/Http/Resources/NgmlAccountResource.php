<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="NgmlAccount",
 *     type="object",
 *     title="NGML Account",
 *     @OA\Property(property="id", type="integer", example=1, description="ID of the NGML account record"),
 *     @OA\Property(property="bank_name", type="string", example="Bank of Example", description="Name of the bank"),
 *     @OA\Property(property="bank_address", type="string", example="123 Bank St, City, Country", description="Address of the bank"),
 *     @OA\Property(property="account_name", type="string", example="John Doe", description="Name associated with the account"),
 *     @OA\Property(property="account_number", type="string", example="123456789", description="Account number"),
 *     @OA\Property(property="sort_code", type="string", example="12-34-56", description="Sort code of the account"),
 *     @OA\Property(property="tin", type="string", example="AB123456789", description="Tax Identification Number (TIN)"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Created at"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Updated at"),
 * )
 */
class NgmlAccountResource extends JsonResource
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
            'bank_name' => $this->bank_name,
            'bank_address' => $this->bank_address,
            'account_name' => $this->account_name,
            'account_number' => $this->account_number,
            'sort_code' => $this->sort_code,
            'tin' => $this->tin,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
