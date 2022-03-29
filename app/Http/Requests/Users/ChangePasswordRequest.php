<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Http\Requests\Users;

use App\Http\Requests\TraitRequest;
use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    use TraitRequest;

    protected function prepareForValidation()
    {
        $this->formFields = ['current_password', 'new_password', 'confirm_password'];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|regex:/^[a-zA-Z0-9]*$/',
            'confirm_password' => 'required|same:new_password',
        ];
    }
}
