<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Enums\Users;

use BenSampo\Enum\Enum;

final class FollowingStatusEnum extends Enum
{
    const NO_FOLLOW = 0;
    const FOLLOWING = 1;
    const WAITING = 2;
}
