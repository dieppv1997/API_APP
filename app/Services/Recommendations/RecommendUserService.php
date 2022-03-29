<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Services\Recommendations;

use App\Interfaces\Repositories\UserFollowRepositoryInterface;
use App\Interfaces\Services\Recommendations\RecommendUserServiceInterface;
use App\Services\BaseService;
use App\Transformers\Users\RecommendUserTransformer;

class RecommendUserService extends BaseService implements RecommendUserServiceInterface
{
    public $userFollowRepository;

    public function __construct(UserFollowRepositoryInterface $userFollowRepository)
    {
        $this->userFollowRepository = $userFollowRepository;
    }

    public function getListRecommendUser($params)
    {
        $checkCurrentFollow = $this->userFollowRepository->checkCurrentFollow()->toArray();
        $listRecommendUsers = $this->userFollowRepository->getListRecommendUser($params, $checkCurrentFollow);
        return fractal($listRecommendUsers, new RecommendUserTransformer());
    }
}
