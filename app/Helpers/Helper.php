<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Helpers;

use App\Enums\Users\UserGenderEnum;
use Carbon\Carbon;

class Helper
{
    public static function postedDateFormat(string $dateTime): string
    {
        $now = Carbon::now();
        $dateToCompare = Carbon::parse($dateTime);
        $diffInMinutes = $now->diffInMinutes($dateToCompare);
        if ($diffInMinutes < 60) {
            if ($diffInMinutes == 0) {
                $diffInMinutes = 1;
            }
            return "{$diffInMinutes}分前";
        } elseif ($diffInMinutes < 1440) {
            $hour = floor($diffInMinutes / 60);
            return "{$hour}時間前";
        } else {
            return $dateToCompare->format('Y/m/d');
        }
    }

    public static function countFormat($count): string
    {
        $countThreshold = config('settings.countThreshold');
        if ($count >= $countThreshold) {
            $underThreshold = $countThreshold - 1;
            return "{$underThreshold}+";
        }
        return (string)$count;
    }

    /**
     * @param $imagePath
     * @return string|null
     */
    public static function generateImageUrl($imagePath): ?string
    {
        if (empty($imagePath)) {
            return null;
        }
        $s3PublicDomain = config('settings.s3PublicDomain');
        return $s3PublicDomain . $imagePath;
    }

    public static function birthdayFormat($date)
    {
        if (!empty($date)) {
            $parse = Carbon::parse($date);
            return $parse->format('F d, Y');
        }
        return $date;
    }

    public static function dateFormat($date)
    {
        if (!empty($date)) {
            $parse = Carbon::parse($date);
            return $parse->format('Y-m-d');
        }
        return $date;
    }

    public static function genderFormat($gender)
    {
        $genderText = $gender;
        if (!empty($gender)) {
            switch ($gender) {
                case UserGenderEnum::MALE:
                    $genderText = trans('messages.user.genderMale');
                    break;
                case UserGenderEnum::FEMALE:
                    $genderText = trans('messages.user.genderFemale');
                    break;
                case UserGenderEnum::OTHER:
                    $genderText = trans('messages.user.genderOther');
                    break;
                default:
                    break;
            }
        }
        return $genderText;
    }
}
