<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class TransactionFilter
 *
 * A class for filtering transactions based on various criteria.
 */
class TransactionFilter
{
    public function __construct(private mixed $filters)
    {
    }

    /**
     * Filter transactions based on the specified date.
     *
     * @param  Builder  $query The Eloquent query builder instance.
     * @param  mixed  $value The date to filter transactions.
     * @return Builder The modified query builder instance.
     */
    public function date(Builder $query, mixed $value): Builder
    {
        $query->whereDate('created_at', $value);

        return $query;
    }

    /**
     * Filter transactions based on the transaction type (income or outcome).
     *
     * @param  Builder  $query The Eloquent query builder instance.
     * @param  string  $type The transaction type ('income' or 'outcome').
     * @return Builder The modified query builder instance.
     */
    public function type(Builder $query, string $type): Builder
    {
        if ($type == 'income') {
            $query->where('amount', '>', 0);
        } elseif ($type == 'outcome') {
            $query->where('amount', '<', 0);
        }

        return $query;
    }

    /**
     * Filter transactions based on the specified amount.
     *
     * @param  Builder  $query The Eloquent query builder instance.
     * @param  mixed  $amount The amount to filter transactions.
     * @return Builder The modified query builder instance.
     */
    public function amount(Builder $query, mixed $amount): Builder
    {
        $query->where('amount', $amount);

        return $query;
    }

    /**
     * Filter transactions based on the specified userId.
     *
     * @param  Builder  $query The Eloquent query builder instance.
     * @return Builder The modified query builder instance.
     */
    public function author(Builder $query, int $userId): Builder
    {
        $query->where('author_id', $userId);

        return $query;
    }

    public function start_date(Builder $query, string $date): Builder
    {
        $query->whereDate('created_at', '>=', $date);

        return $query;
    }

    public function end_date(Builder $query, string $date): Builder
    {
        $query->whereDate('created_at', '<=', $date);

        return $query;
    }

    /**
     * Apply the specified filters to the transaction query.
     *
     * @param  Builder  $query The Eloquent query builder instance.
     */
    public function apply(Builder $query): void
    {
        foreach ($this->filters as $filterName => $value) {
            if (method_exists($this, $filterName)) {
                $this->$filterName($query, $value);
            }
        }
    }
}
