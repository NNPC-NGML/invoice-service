<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="LetterTemplate",
 *     type="object",
 *     title="Letter Template",
 *     @OA\Property(property="id", type="integer", example=1, description="ID of the letter template"),
 *     @OA\Property(property="letter", type="string", example="Sample Letter Content", description="Content of the letter template"),
 *     @OA\Property(property="status", type="integer", example=1, description="Status of the letter template"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Created at"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Updated at"),
 * )
 */
class LetterTemplateResource extends JsonResource
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
            'letter' => $this->letter,
            'status' => $this->status,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
