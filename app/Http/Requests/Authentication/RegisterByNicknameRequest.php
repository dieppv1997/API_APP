<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Http\Requests\Authentication;

use App\Http\Requests\TraitRequest;
use App\Http\Requests\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterByNicknameRequest extends FormRequest
{
    use TraitRequest;

    protected function prepareForValidation()
    {
        $this->formFields = ['nickname', 'province_id'];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'nickname' => ValidationRule::nickNameRule(),
            'province_id' => ValidationRule::provinceIdRule(),
            'device_id' => 'required',
        ];
    }
}
