<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Http\Requests;

use App\Enums\Users\UserGenderEnum;
use Illuminate\Validation\Rule;

class ValidationRule
{
    public static function genderRule(): array
    {
        return [
            'nullable',
            Rule::in([UserGenderEnum::MALE, UserGenderEnum::FEMALE, UserGenderEnum::OTHER]),
        ];
    }

    public static function profileImageRule(): array
    {
        return [
            'nullable',
            'image',
            'mimes:jpeg,png,jpg',
            'mimetypes:image/jpeg,image/png,image/jpg',
            'max:' . config('settings.maxKbImageUpload'),
        ];
    }

    public static function provinceIdRule(): string
    {
        return 'required|exists:provinces,id';
    }

    public static function nickNameRule(): string
    {
        return 'required|string|max:30|min:1|not_regex:/　/
            |regex:/^[(a-zA-Z0-9)(ぁ-んァ-ン)(一ー-龯)(\\\)(、。，,―‐_\/・～…‥：；？！＃＆＊※＠’”()°℃￥＋－×÷Ⅰ～ⅩⅠ～Ⅹ)]*$/';
    }
}
