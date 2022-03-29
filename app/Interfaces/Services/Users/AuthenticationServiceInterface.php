<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Interfaces\Services\Users;

interface AuthenticationServiceInterface
{
    public function registerEmail($params);

    public function registerEmailNickname($params);

    public function verifyEmail($params);

    public function verifyEmailNickname($params);

    public function registerByNickname($params);

    public function login($params);

    public function logout($params);
}
