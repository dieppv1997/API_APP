<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class UnknownException extends AbstractException
{
    public function __construct($message = '', $code = null)
    {
        if (!$message) {
            $message = trans('exception.bad_request');
        }
        if (!$code) {
            $code = Response::HTTP_BAD_REQUEST;
        }
        parent::__construct($message, $code);
    }
}
