<?php

namespace App\Providers;

use App\Events\TransactionSuccess;
use App\Listeners\SendTransactionNotification;
use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        TransactionSuccess::class => [
            SendTransactionNotification::class,
        ],
    ];
}
