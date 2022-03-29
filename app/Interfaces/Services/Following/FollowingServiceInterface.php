<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Interfaces\Services\Following;

interface FollowingServiceInterface
{
    public function followUser($params);

    public function unFollowUser($params);

    public function approveFollow($params);

    public function rejectFollow($params);
}
