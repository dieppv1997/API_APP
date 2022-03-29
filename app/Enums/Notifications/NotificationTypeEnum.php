<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Enums\Notifications;

use BenSampo\Enum\Enum;

final class NotificationTypeEnum extends Enum
{
    const ACTIVITY = 'activity';
    const FOLLOW_REQUEST = 'follow_request';
    const OFFICIAL = 'official';
}
