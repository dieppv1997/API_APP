<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;
use Symfony\Component\HttpFoundation\Response;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var string[]
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var string[]
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->renderable(function (Throwable $e) {
            $throwable = false;
            $messages = [
                Response::HTTP_INTERNAL_SERVER_ERROR => trans('exception.server_error'),
                Response::HTTP_METHOD_NOT_ALLOWED => trans('exception.method_not_allowed'),
                Response::HTTP_UNAUTHORIZED => trans('exception.unauthorized'),
                Response::HTTP_FORBIDDEN => trans('exception.forbidden'),
                Response::HTTP_BAD_REQUEST => trans('exception.bad_request'),
                Response::HTTP_NOT_FOUND => trans('exception.not_found'),
            ];
            if ($e instanceof \ParseError) {
                $code = Response::HTTP_INTERNAL_SERVER_ERROR;
                $throwable = true;
            } elseif ($e instanceof HttpException) {
                $statusCode = $e->getStatusCode();
                switch ($statusCode) {
                    case Response::HTTP_NOT_FOUND:
                    case Response::HTTP_METHOD_NOT_ALLOWED:
                    case Response::HTTP_UNAUTHORIZED:
                    case Response::HTTP_FORBIDDEN:
                    case Response::HTTP_BAD_REQUEST:
                        $throwable = true;
                        $code = $statusCode;
                        break;
                    default:
                        break;
                }
            } else {
                //continue
            }

            if ($throwable) {
                return response()->json([
                    'message' => $messages[$code]
                ], $code);
            }
        });
    }
}
