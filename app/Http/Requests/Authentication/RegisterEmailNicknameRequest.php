<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Http\Requests\Authentication;

use App\Http\Requests\TraitRequest;
use Illuminate\Foundation\Http\FormRequest;

class RegisterEmailNicknameRequest extends FormRequest
{
    use TraitRequest;

    protected function prepareForValidation()
    {
        $this->formFields = ['email', 'password'];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|string|email|max:50',
            'password' => 'required|string|min:8|regex:/^[a-zA-Z0-9]*$/',
        ];
    }
}
