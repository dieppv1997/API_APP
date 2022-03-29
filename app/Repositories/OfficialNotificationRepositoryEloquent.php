<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Repositories;

use App\Interfaces\Repositories\OfficialNotificationRepositoryInterface;
use App\Models\OfficialNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class OfficialNotificationRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class OfficialNotificationRepositoryEloquent extends BaseRepository implements OfficialNotificationRepositoryInterface
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return OfficialNotification::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function getAvailableByDate($date)
    {
        return $this->model
            ->where('start_show_date', '<=', $date)
            ->where('end_show_date', '>=', $date)
            ->where('deleted_at', null)
            ->orderBy('start_show_date', 'desc');
    }

    /**
     * @param $date
     * @param $userId
     * @return mixed
     */
    public function getUnreadByDateUserId($date, $userId)
    {
        return $this->model
            ->where('start_show_date', '<=', $date)
            ->where('end_show_date', '>=', $date)
            ->whereNotExists(function ($query) use ($userId) {
                $query->select(DB::raw(1))
                    ->from('notifications')
                    ->whereRaw('notifiable_id = official_notifications.id')
                    ->where([
                        'receiver_id' => $userId,
                        'notifiable_type' => 'App\Models\OfficialNotification',
                    ]);
            });
    }
}
