<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Enums\Notifications;

use BenSampo\Enum\Enum;

final class NotificationTemplateNameEnum extends Enum
{
    const LIKE_POST = 'LIKE_POST';
    const LIKE_COMMENT = 'LIKE_COMMENT';
    const NEW_COMMENT = 'NEW_COMMENT';
    const FOLLOW_USER_PUBLIC = 'FOLLOW_USER_PUBLIC';
    const FOLLOW_USER_PRIVATE = 'FOLLOW_USER_PRIVATE';
    const NEW_REPLY_COMMENT = 'NEW_REPLY_COMMENT';
    const OFFICIAL = 'OFFICIAL';
}
