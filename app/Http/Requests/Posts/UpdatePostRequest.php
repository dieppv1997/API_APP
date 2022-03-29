<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Http\Requests\Posts;

class UpdatePostRequest extends CreateUpdatePostRequest
{
    protected function prepareForValidation()
    {
        $this->prepareParams();
        $this->merge(['postId' => $this->route('postId')]);
        $image = $this->request->get('image');
        if (!empty($image)) {
            $this->requireOriginalImage = true;
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $rules = $this->baseRules();
        $rules['postId'] = 'required|integer|min:1';
        return $rules;
    }
}
