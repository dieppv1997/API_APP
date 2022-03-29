<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Traits;

use App\Enums\Users\FollowingStatusEnum;
use App\Models\UserFollow;

trait FollowingTrait
{
    /**
     * @param UserFollow|array $record
     * @return bool
     */
    public function isWaiting($record)
    {
        if (is_array($record)) {
            return $record['status'] == FollowingStatusEnum::WAITING;
        }
        if (is_object($record)) {
            return $record->status == FollowingStatusEnum::WAITING;
        }
        return false;
    }

    /**
     * @param UserFollow|array $record
     * @return bool
     */
    public function isFollowing($record)
    {
        if (is_array($record)) {
            return $record['status'] == FollowingStatusEnum::FOLLOWING;
        }
        if (is_object($record)) {
            return $record->status == FollowingStatusEnum::FOLLOWING;
        }
        return false;
    }
}
