<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Repositories;

use App\Interfaces\Repositories\ChangeEmailRequestRepositoryInterface;
use App\Models\ChangeEmailRequest;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class VerifyUserRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ChangeEmailRequestRepositoryEloquent extends BaseRepository implements ChangeEmailRequestRepositoryInterface
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ChangeEmailRequest::class;
    }
    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
