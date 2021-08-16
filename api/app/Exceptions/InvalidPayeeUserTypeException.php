<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;

class InvalidPayeeUserTypeException extends Exception
{
    protected $message = 'Tipo de usuário inválido';
    protected $code = Response::HTTP_UNPROCESSABLE_ENTITY;
}
