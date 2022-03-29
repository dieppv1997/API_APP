<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Http\Requests\Posts;

use App\Http\Requests\TraitRequest;
use Illuminate\Foundation\Http\FormRequest;

class ShowPostForEditRequest extends FormRequest
{
    use TraitRequest;
    protected function prepareForValidation()
    {
        $this->merge(['postId' => $this->route('postId')]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'postId' => 'required|integer|min:1',
        ];
    }
}
