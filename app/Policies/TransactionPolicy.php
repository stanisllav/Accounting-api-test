<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;

class TransactionPolicy
{
    public function show(User $user, Transaction $transaction): bool
    {
        return $transaction->author()->is($user);
    }

    public function delete(User $user, Transaction $transaction): bool
    {
        return $transaction->author()->is($user);
    }
}
