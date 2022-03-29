<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Http\Requests\Users;

use App\Http\Requests\TraitRequest;
use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    use TraitRequest;

    protected function prepareForValidation()
    {
        $this->formFields = ['email', 'password', 'password_confirmation'];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => 'required|string|email',
            'password' => 'required|string|min:8|max:255|confirmed|regex:/^[a-zA-Z0-9]*$/',
            'token' => 'required|string',
        ];
    }
}
