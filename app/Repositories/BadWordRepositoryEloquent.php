<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Repositories;

use App\Interfaces\Repositories\BadWordRepositoryInterface;
use App\Models\BadWord;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class VerifyUserRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class BadWordRepositoryEloquent extends BaseRepository implements BadWordRepositoryInterface
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return BadWord::class;
    }
    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
