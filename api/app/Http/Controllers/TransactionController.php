<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Events\TransactionSuccess;
use App\Services\TransactionService;
use App\Exceptions\InsufficientBalanceException;
use App\Exceptions\UnauthorizedPaymentException;
use App\Exceptions\InvalidPayeeUserTypeException;

class TransactionController extends Controller
{
    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'payer_id' => 'required|exists:users,id',
            'payee_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
        ]);

        try {
            $transaction = $this->transactionService->handle($request->all());

            event(new TransactionSuccess($transaction));

            return response()->json([
                'message' => 'Transação realizada com sucesso.',
                'transaction' => $transaction
            ], Response::HTTP_OK);
        } catch (InvalidPayeeUserTypeException |
            InsufficientBalanceException |
            UnauthorizedPaymentException $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
