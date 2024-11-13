<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="GccApprovedByAdmin",
 *     type="object",
 *     title="GCC Approved by Admin",
 *     @OA\Property(property="id", type="integer", example=1, description="ID of the approval record"),
 *     @OA\Property(property="user_id", type="integer", example=2, description="ID of the user who approved"),
 *     @OA\Property(property="invoice_advice_id", type="integer", example=3, description="ID of the associated invoice advice"),
 *     @OA\Property(property="date", type="string", format="date-time", example="2024-09-23T10:00:00Z", description="Approval date"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Created at"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Updated at"),
 * )
 */
class GccApprovedByAdminResource extends JsonResource
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
            'date' => $this->date->toDateTimeString(),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
