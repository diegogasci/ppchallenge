<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;

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
        if (!$this->transferAmountBiggerThanZero($data['amount'])) {
            throw new \Exception('Valor de transferência inválido', 422);
        }

        $payer = $this->userRepository->getUser($data['payer_id']);

        if (!$this->userCanTransfer($payer)) {
            throw new \Exception('Tipo de usuário inválido', 422);
        }

        if (!$this->userHasSufficientBalance($payer, $data['amount'])) {
            throw new \Exception('Saldo insuficiente para a transferência', 422);
        }

        if (!$this->authorizationService->check()) {
            throw new \Exception('Transação não autorizada.', 401);
        }

        $payee = User::with('wallet')->findOrFail($data['payee_id']);

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

    /**
     * Checks if transfer amount is bigger than zero.
     *
     * @return bool
     */
    private function transferAmountBiggerThanZero($transferAmount)
    {
        return $transferAmount > 0;
    }
}
