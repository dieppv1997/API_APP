<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Http\Controllers;

use App\Exceptions\CheckAuthenticationException;
use App\Exceptions\CheckAuthorizationException;
use App\Exceptions\NotFoundException;
use App\Exceptions\QueryException;
use App\Exceptions\ServerException;
use App\Exceptions\UnprocessableEntityException;
use App\Exceptions\BadRequestException;
use App\Traits\HandleExceptionTrait;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    use HandleExceptionTrait;

    /**
     * @param $response
     * @param int $code
     * @return JsonResponse
     */
    public function sendResponse($response, int $code = Response::HTTP_OK): JsonResponse
    {
        return response()->json($response, $code);
    }


    /**
     * @param $errorMsg
     * @param int $code
     * @return JsonResponse
     */
    public function sendError($errorMsg, int $code = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        $response = [
            'message' => $errorMsg,
        ];

        return response()->json($response, $code);
    }

    /**
     * get the data for the GET api method
     *
     * @param callable $callback
     * @param int $code
     * @return JsonResponse
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws CheckAuthorizationException
     * @throws CheckAuthenticationException
     * @throws UnprocessableEntityException
     */
    protected function getData(callable $callback, $request, int $code = Response::HTTP_OK): JsonResponse
    {
        try {
            $data = call_user_func_array($callback, []);
            return $this->sendResponse($data, $code);
        } catch (UnprocessableEntityException $e) {
            $this->handleException($e, $request);
            throw new UnprocessableEntityException($e->getMessage());
        } catch (QueryException $e) {
            $this->handleException($e, $request);
            throw new QueryException($e->getMessage());
        } catch (NotFoundException $e) {
            $this->handleException($e, $request);
            throw new NotFoundException($e->getMessage());
        } catch (ModelNotFoundException $e) {
            $this->handleException($e, $request);
            throw new NotFoundException(trans('exception.record_not_found'));
        } catch (CheckAuthorizationException $e) {
            $this->handleException($e, $request);
            throw new CheckAuthorizationException($e->getMessage());
        } catch (CheckAuthenticationException $e) {
            $this->handleException($e, $request);
            throw new CheckAuthenticationException($e->getMessage());
        } catch (Exception $e) {
            $this->handleException($e, $request);
            throw new ServerException();
        }
    }

    /**
     * Process the request to the server with the external method Get
     *
     * @param callable $callback
     * @param $request
     * @param int $code
     * @return JsonResponse
     * @throws NotFoundException
     * @throws QueryException
     * @throws BadRequestException
     * @throws ServerException
     * @throws CheckAuthorizationException
     * @throws UnprocessableEntityException
     */
    protected function doRequest(callable $callback, $request, int $code = Response::HTTP_OK): JsonResponse
    {
        DB::beginTransaction();
        try {
            $results = call_user_func_array($callback, []);
            DB::commit();
            return $this->sendResponse($results, $code);
        } catch (UnprocessableEntityException $e) {
            DB::rollBack();
            $this->handleException($e, $request);
            throw new UnprocessableEntityException($e->getMessage());
        } catch (QueryException $e) {
            DB::rollBack();
            $this->handleException($e, $request);
            throw new QueryException($e->getMessage());
        } catch (BadRequestException $e) {
            DB::rollBack();
            $this->handleException($e, $request);
            throw new BadRequestException($e->getMessage());
        } catch (NotFoundException $e) {
            DB::rollBack();
            $this->handleException($e, $request);
            throw new NotFoundException($e->getMessage());
        } catch (ModelNotFoundException $e) {
            $this->handleException($e, $request);
            throw new NotFoundException(trans('exception.record_not_found'));
        } catch (CheckAuthorizationException $e) {
            DB::rollBack();
            $this->handleException($e, $request);
            throw new CheckAuthorizationException($e->getMessage());
        } catch (Exception $e) {
            DB::rollBack();
            $this->handleException($e, $request);
            throw new ServerException();
        }
    }
}
