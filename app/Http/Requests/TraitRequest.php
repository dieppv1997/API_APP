<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Http\Requests;

use App\Traits\HandleExceptionTrait;
use App\Traits\LoggingTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

trait TraitRequest
{
    use HandleExceptionTrait, LoggingTrait;

    public $formFields = [];
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Boot the soft deleting trait for a model.
     * @param Validator $validator
     */
    public function failedValidation(Validator $validator)
    {
        $allMessage = $validator->errors()->getMessages();
        $errorMessages = [];
        $formFieldErrorMessages = [];
        foreach ($allMessage as $key => $item) {
            if (in_array($key, $this->formFields)) {
                $formFieldErrorMessages[$key] = $item[0];
            } else {
                $keyParse = explode('.', $key);
                if (count($keyParse) == 2 && in_array($keyParse[0], $this->formFields)) {
                    $formFieldErrorMessages[$keyParse[0]] = $item[0];
                }
                $errorMessages[] = $item[0];
            }
        }
        $this->logValidateErrorData($allMessage);
        if (!empty($formFieldErrorMessages)) {
            $formFieldErrorMessages = array_unique($formFieldErrorMessages);
            throw new HttpResponseException(
                response()->json([
                    'message' => $formFieldErrorMessages,
                ], Response::HTTP_UNPROCESSABLE_ENTITY)
            );
        }
        if (!empty($errorMessages)) {
            throw new HttpResponseException(
                response()->json([
                    'message' => trans('exception.bad_request'),
                ], Response::HTTP_BAD_REQUEST)
            );
        }
    }

    /**
     * Set default if empty value for current_page and per_page
     * @param int $currentPage
     * @param int $perPage
     */
    public function prepareForPagination(int $currentPage = 1, int $perPage = 10)
    {
        $request = Request::all();
        if (!isset($request['current_page'])) {
            $this->merge(['current_page' => $currentPage]);
        }
        if (!isset($request['per_page'])) {
            $this->merge(['per_page' => $perPage]);
        }
    }

    private function logValidateErrorData($logs)
    {
        $this->collectRequestInformation($this, $logs);
        $this->writeLogToLoggingChannel('errorlog', $logs);
    }
}
