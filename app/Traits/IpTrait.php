<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Traits;

trait IpTrait
{
    public function getClientIp()
    {
        $ips = [
            getenv('HTTP_CLIENT_IP'),
            getenv('HTTP_X_FORWARDED_FOR'),
            getenv('HTTP_X_FORWARDED'),
            getenv('HTTP_FORWARDED_FOR'),
            getenv('HTTP_FORWARDED'),
            getenv('REMOTE_ADDR'),
        ];
        foreach ($ips as $ip) {
            if ($ip) {
                $arrayIPs = explode(",", $ip);
                return reset($arrayIPs);
            }
        }
        return config('settings.nullIpAddress');
    }
}
