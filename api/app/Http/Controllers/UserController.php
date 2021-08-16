<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\UserRepository;

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
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6',
            'document_type' => 'required|in:0,1',
            'document' => 'required|min:11',
        ]);

        $requestData = $request->all();

        $user = $this->userRepository->createUser($requestData);

        return $user;
    }

    public function update(Request $request, $userId)
    {
        $this->validate($request, [
            'name' => 'required_without_all:email,password',
            'email' => 'email|required_without_all:name,password',
            'password' => 'min:6|required_without_all:name,email',
        ]);

        $user = $this->userRepository->getUser($userId);
        $user->update($request->only([
            'name', 'email', 'password'
        ]));

        return $user;
    }

    public function delete($userId)
    {
        $user = $this->userRepository->getUser($userId);
        $user->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function wallet($userId)
    {
        $user = $this->userRepository->getUser($userId);

        return $user->wallet;
    }
}
