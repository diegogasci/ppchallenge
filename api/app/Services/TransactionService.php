<?php

namespace App\Services;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use App\Repositories\UserRepository;
use App\Services\AuthorizationService;
use App\Exceptions\InsufficientBalanceException;
use App\Exceptions\UnauthorizedPaymentException;
use App\Exceptions\InvalidPayeeUserTypeException;

class TransactionService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository, AuthorizationService $authorizationService)
    {
        $this->userRepository = $userRepository;
        $this->authorizationService = $authorizationService;
    }

    public function handle($data)
    {
        $payer = $this->userRepository->getUser($data['payer_id']);

        if (!$this->userCanTransfer($payer)) {
            throw new InvalidPayeeUserTypeException;
        }

        if (!$this->userHasSufficientBalance($payer, $data['amount'])) {
            throw new InsufficientBalanceException;
        }

        if (!$this->authorizationService->check()) {
            throw new UnauthorizedPaymentException;
        }

        $payee = $this->userRepository->getUser($data['payee_id']);

        $transaction = DB::transaction(function () use ($payer, $payee, $data) {
            $payer->wallet->withdraw($data['amount']);
            $payee->wallet->deposit($data['amount']);

            return Transaction::create($data);
        });

        return $transaction;
    }

    /**
     * Checks if user type (common user or shopkeeper) can transfer money.
     *
     * @return bool
     */
    private function userCanTransfer(User $payer)
    {
        return intval($payer->document_type) === UserRepository::COMMONUSER;
    }

    /**
     * Checks if user balance is sufficient to transfer.
     *
     * @return bool
     */
    private function userHasSufficientBalance(User $payer, $transferAmount)
    {
        return $payer->wallet->balance >= $transferAmount;
    }
}
