<?php

namespace App\Exceptions;

use Exception;

class UnauthorizedPaymentException extends Exception
{
    protected $message = 'Transação não autorizada';
    protected $code = Response::HTTP_UNAUTHORIZED;
}
