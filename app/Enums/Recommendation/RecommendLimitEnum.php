<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Enums\Recommendation;

use BenSampo\Enum\Enum;

final class RecommendLimitEnum extends Enum
{
    const LIMIT_RECOMMEND_USER = 5;
    const LIMIT_RECOMMEND_TAG = 3;
    const LIMIT_POST_OF_RECOMMEND_TAG = 9;
}
