<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Http\Requests\Comments;

use App\Http\Requests\TraitRequest;
use Illuminate\Foundation\Http\FormRequest;

class PostCommentRequest extends FormRequest
{
    use TraitRequest;

    protected function prepareForValidation()
    {
        $this->formFields = ['content'];
        $this->merge(['postId' => $this->route('postId')]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'postId' => 'required|integer',
            'parent_id' => 'nullable|integer',
            'content' => 'required|string|max:2000'
        ];
    }
}
