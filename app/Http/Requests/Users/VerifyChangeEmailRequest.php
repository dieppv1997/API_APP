<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Http\Requests\Users;

use App\Http\Requests\TraitRequest;
use Illuminate\Foundation\Http\FormRequest;

class VerifyChangeEmailRequest extends FormRequest
{
    use TraitRequest;

    protected function prepareForValidation()
    {
        $this->formFields = ['verify_code'];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'verify_code' => 'required|string',
            'email' => 'required|string|email|max:50'
        ];
    }
}
