<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Enums\Settings;

use BenSampo\Enum\Enum;

final class SettingNameEnum extends Enum
{
    const ENABLE_NOTIFICATION_POST = 'enable_notification_post';
    const ENABLE_NOTIFICATION_COMMENT = 'enable_notification_comment';
    const ENABLE_NOTIFICATION_FOLLOWING = 'enable_notification_following';
}
