<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function show(Request $request)
    {
        $user = User::findOrFail($request->userId);

        return $user;
    }

    public function all()
    {
        return User::with('wallet')->get();
    }

    public function create(Request $request)
    {
        $requestData = $request->all();
        $requestData['password'] = Hash::make($request->password);

        $user = User::create($requestData);

        return $user;
    }

    public function update(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        $user->update($request->all());

        return $user;
    }

    public function delete(User $user)
    {
        $user->delete();

        return response()->json(null, 204);
    }

    public function transactions($userId)
    {
        $user = User::findOrFail($userId);

        return $user->transactions;
    }

    public function wallet($userId)
    {
        $user = User::findOrFail($userId);

        return $user->wallet;
    }
}
