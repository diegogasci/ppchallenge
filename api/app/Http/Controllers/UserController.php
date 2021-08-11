<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function show(Request $request)
    {
        $user = $this->userRepository->getUser($request->userId);

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
        $user = $this->userRepository->getUser($userId);
        $user->update($request->all());

        return $user;
    }

    public function delete($userId)
    {
        $user = $this->userRepository->getUser($userId);
        $user->delete();

        return response()->json(null, 204);
    }

    public function wallet($userId)
    {
        $user = $this->userRepository->getUser($userId);

        return $user->wallet;
    }
}
