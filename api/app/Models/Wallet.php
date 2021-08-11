<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = [
        'balance', 'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function withdraw($value)
    {
        $this->update([
            'balance' => $this->attributes['balance'] - $value
        ]);
    }

    public function deposit($value)
    {
        $this->update([
            'balance' => $this->attributes['balance'] + $value
        ]);
    }
}
