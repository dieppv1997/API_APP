<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Http\Requests\Users;

use App\Http\Requests\TraitRequest;
use App\Http\Requests\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    use TraitRequest;
    use TraitRequest;
    protected function prepareForValidation()
    {
        $this->formFields = [
            'nickname',
            'avatar_image',
            'cover_image',
            'birthday',
            'gender',
            'favorite_shop',
            'favorite_place',
            'intro',
            'province_id'
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'nickname' => ValidationRule::nickNameRule(),
            'avatar_image' => ValidationRule::profileImageRule(),
            'cover_image' => ValidationRule::profileImageRule(),
            'birthday' => 'nullable|date_format:Y-m-d',
            'gender' => ValidationRule::genderRule(),
            'favorite_shop' => 'nullable|string|max:50',
            'favorite_place' => 'nullable|string|max:50',
            'intro' => 'nullable|string|max:150',
            'province_id' => ValidationRule::provinceIdRule(),
        ];
    }
}
