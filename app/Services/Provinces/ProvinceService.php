<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Services\Provinces;

use App\Interfaces\Repositories\ProvinceRepositoryInterface;
use App\Interfaces\Services\Provinces\ProvinceServiceInterface;
use App\Services\BaseService;
use App\Transformers\Provinces\ProvinceTransformer;

class ProvinceService extends BaseService implements ProvinceServiceInterface
{
    /**
     * @param ProvinceRepositoryInterface $repository
     */
    public function __construct(ProvinceRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getList($langCode)
    {
        $listProvince = $this->repository->getList($langCode);
        return fractal($listProvince, new ProvinceTransformer());
    }
}
