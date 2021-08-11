<?php

namespace App\Listeners;

use App\Events\TransactionSuccess;
use Illuminate\Support\Facades\Http;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendTransactionNotification implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\TransactionSuccess  $event
     * @return void
     */
    public function handle(TransactionSuccess $event)
    {
        $transaction = $event->transaction;

        Http::request('GET', 'http://o4d9z.mocklab.io/notify', [
            'query' => [
                'payer' => $transaction->payer->nome,
                'payee' => $transaction->payee->nome,
            ]
        ]);
    }
}
