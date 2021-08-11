<?php

namespace App\Http\Controllers;

use App\Events\TransactionSuccess;
use App\Services\TransactionService;
use Illuminate\Http\Request;

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

            return response()->json(['message' => 'Transação realizada com sucesso.'], 200);
        } catch (\Exception $e) {
            $statusCode = $e->getCode();

            if ($statusCode < 200) {
                $statusCode = 500;
            }

            return response()->json(['message' => $e->getMessage()], $statusCode);
        }
    }
}
