<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Enums\Logging;

use BenSampo\Enum\Enum;

final class LogTypeEnum extends Enum
{
    const USER_REQUEST   = 'USER_REQUEST';
    const SYSTEM_COMMAND = 'SYSTEM_COMMAND';
    const UNKNOWN        = 'UNKNOWN';
}
