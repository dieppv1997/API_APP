<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Interfaces\Services\Users;

interface UserServiceInterface
{
    public function getDetail($user);

    public function profile($params);

    public function updateProfile($params);

    public function getListFollowers($params);

    public function getListFollowing($params);
}
