<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'TransactionResource',
    title: 'TransactionResource',
    description: 'TransactionResource',
    properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'title', type: 'string'),
        new OA\Property(property: 'amount', type: 'number', format: 'float'),
        new OA\Property(property: 'author', ref: '#/components/schemas/UserResource'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date'),
    ],
    type: 'object'
)]
class TransactionResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'amount' => $this->amount,
            'author' => UserResource::make($this->author),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
