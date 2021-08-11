<?php

namespace App\Events;

use App\Models\Transaction;

class TransactionSuccess extends Event
{
    protected $transaction;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }
}
