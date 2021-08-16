<?php

namespace App\Repositories;

use App\Models\Transaction;

class TransactionRepository
{
    public function createTransaction(array $data)
    {
        return Transaction::create($data);
    }

    public function getTransaction($transactionId)
    {
        return Transaction::findOrFail($transactionId);
    }
}
