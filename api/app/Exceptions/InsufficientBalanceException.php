<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;

class InsufficientBalanceException extends Exception
{
    protected $message = 'Saldo insuficiente para a transferência';
    protected $code = Response::HTTP_UNPROCESSABLE_ENTITY;
}
