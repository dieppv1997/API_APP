<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Http\Requests\Posts;

use App\Http\Requests\TraitRequest;
use Illuminate\Foundation\Http\FormRequest;

class ListPostLikedRequest extends FormRequest
{
    use TraitRequest;

    protected function prepareForValidation()
    {
        $this->prepareForPagination();
        $this->merge(['userId' => $this->route('userId')]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'userId' => 'required|integer|min:1',
            'current_page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1'
        ];
    }
}
