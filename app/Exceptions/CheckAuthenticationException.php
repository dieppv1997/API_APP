<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class CheckAuthenticationException extends AbstractException
{
    /**
     * CheckAuthenticationException constructor.
     * @param string $message
     * @param null $code
     */
    public function __construct($message = '', $code = null)
    {
        if (!$message) {
            $message = trans('exception.unauthorized');
        }

        if (!$code) {
            $code = Response::HTTP_UNAUTHORIZED;
        }
        parent::__construct($message, $code);
    }
}
