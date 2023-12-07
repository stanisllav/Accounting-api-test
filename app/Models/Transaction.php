<?php

namespace App\Models;

use App\Events\TransactionCreated;
use App\Filters\TransactionFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Transaction',
    title: 'Transaction',
    description: 'Transaction Model',
    properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'title', type: 'string'),
        new OA\Property(property: 'amount', type: 'number', format: 'float'),
        new OA\Property(property: 'author_id', type: 'integer'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date'),
    ],
    type: 'object'
)]
class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $dispatchesEvents = [
        'created' => TransactionCreated::class,
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeFilter(Builder $query, TransactionFilter $filter): Builder
    {
        $filter->apply($query);

        return $query;
    }
}
