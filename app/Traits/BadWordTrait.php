<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Traits;

use App\Models\BadWord;

trait BadWordTrait
{
    public function getBadWord()
    {
        $badWordRepository = app('App\Interfaces\Repositories\BadWordRepositoryInterface');
        return $badWordRepository->pluck('word')->toArray();
    }

    public function isBadWord($content, $badWord)
    {
        $lowerContent = strtolower($content);
        foreach ($badWord as $item) {
            $lowerBadWord = strtolower($item);
            if (strpos($lowerContent, $lowerBadWord) !== false) {
                return true;
            }
        }
        return false;
    }
}
