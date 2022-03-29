<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

abstract class AbstractException extends Exception
{
    protected $code;
    protected $message;

    /**
     * AbstractException constructor.
     * @param null $message
     * @param int $code
     */
    public function __construct($message = null, $code = Response::HTTP_INTERNAL_SERVER_ERROR)
    {
        $this->code = $code;
        $this->message = $message ?: trans('exception.server_error');

        parent::__construct($message, $code);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function render(Request $request)
    {
        return response()->json([
            'message' => $this->message
        ], $this->code);
    }

    /**
     * Log an exception.
     */
    public function report()
    {
        Log::emergency($this->message);
    }
}
