<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Enums\Users;

use BenSampo\Enum\Enum;

final class RegisterTypeEnum extends Enum
{
    const REGISTER_EMAIL = 1;
    const REGISTER_NICKNAME = 2;
    const REGISTER_LINE = 3;
}
