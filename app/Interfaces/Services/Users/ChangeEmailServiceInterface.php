<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Interfaces\Services\Users;

interface ChangeEmailServiceInterface
{
    public function requestChangeEmail($params);

    public function verifyChangeEmail($params);
}
