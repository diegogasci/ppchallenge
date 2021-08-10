<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    const COMMONUSER = 0;
    const SHOPKEEPER = 0;

    public function createUser(array $data)
    {
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        return $user;
    }

    public function getUser($userId)
    {
        return User::with('wallet')->findOrFail($userId);
    }
}
