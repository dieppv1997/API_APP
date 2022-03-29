<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Repositories;

use App\Interfaces\Repositories\NotificationTemplateRepositoryInterface;
use App\Models\NotificationTemplate;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class NotificationTemplateRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class NotificationTemplateRepositoryEloquent extends BaseRepository implements NotificationTemplateRepositoryInterface
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return NotificationTemplate::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
