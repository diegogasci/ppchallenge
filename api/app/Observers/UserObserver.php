<?php

namespace App\Observers;

use App\Models\User;

trait UserObserver
{
    protected static function booted()
    {
        parent::booted();

        static::created(function (User $user) {
            $user->wallet()->create(['balance' => 0]);
        });
    }
}
