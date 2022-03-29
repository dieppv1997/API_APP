<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Http\Controllers\Provinces;

use App\Exceptions\CheckAuthenticationException;
use App\Exceptions\CheckAuthorizationException;
use App\Exceptions\NotFoundException;
use App\Exceptions\QueryException;
use App\Exceptions\ServerException;
use App\Exceptions\UnprocessableEntityException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Provinces\ProvinceListRequest;
use App\Interfaces\Services\Provinces\ProvinceServiceInterface;
use Illuminate\Http\JsonResponse;

class ProvinceController extends Controller
{
    /**
     * @var ProvinceServiceInterface
     */
    protected $provinceService;

    public function __construct(ProvinceServiceInterface $provinceService)
    {
        $this->provinceService = $provinceService;
    }

    /**
     * @param ProvinceListRequest $request
     * @return JsonResponse
     * @throws CheckAuthenticationException
     * @throws CheckAuthorizationException
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws UnprocessableEntityException
     */
    public function getList(ProvinceListRequest $request): JsonResponse
    {
        $params = $request->all();
        return $this->getData(function () use ($params) {
            return $this->provinceService->getList($params['lang_code']);
        }, $request);
    }
}
