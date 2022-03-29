<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Http\Requests\Search;

use App\Http\Requests\TraitRequest;
use Illuminate\Foundation\Http\FormRequest;

class SearchAllPostByKeyFromIdRequest extends FormRequest
{
    use TraitRequest;

    protected function prepareForValidation()
    {
        $this->prepareForPagination();
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'keyword' => 'nullable|string|max:50',
            'postId' => 'required|integer|min:1',
            'current_page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1'
        ];
    }
}
