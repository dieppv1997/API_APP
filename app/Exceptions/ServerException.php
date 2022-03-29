<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class ServerException extends AbstractException
{
    public function __construct($message = '', $code = null)
    {
        if (!$message) {
            $message = trans('exception.server_error');
        }

        if (!$code) {
            $code = Response::HTTP_INTERNAL_SERVER_ERROR;
        }
        parent::__construct($message, $code);
    }
}
