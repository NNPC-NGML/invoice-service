<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     title="User",
 *     description="User resource schema",
 *     @OA\Property(property="id", type="integer", example=1, description="ID of the user"),
 *     @OA\Property(property="name", type="string", example="John Doe", description="Name of the user"),
 *     @OA\Property(property="email", type="string", format="email", example="john.doe@example.com", description="Email address of the user"),
 * )
 */
class UserResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
        ];
    }
}
