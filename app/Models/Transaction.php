<?php

namespace App\Models;

use App\Events\TransactionCreated;
use App\Filters\TransactionFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

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
