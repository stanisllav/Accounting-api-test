<?php

namespace App\DTO;

use App\Models\User;

final readonly class TransactionDTO
{
    public function __construct(
        public string $title,
        public float $amount,
        public User $author
    ) {
    }
}
