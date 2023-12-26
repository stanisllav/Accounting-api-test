<?php

namespace App\Services;

use App\DTO\TransactionDTO;
use App\Models\Transaction;

class TransactionsService
{
    public function createTransaction(TransactionDTO $dto): Transaction
    {

        $transaction = new Transaction;
        $transaction->title = $dto->title;
        $transaction->amount = $dto->amount;
        $transaction->author()->associate($dto->author);
        $transaction->save();

        return $transaction;
    }
}
