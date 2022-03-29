<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Http\Requests\Posts;

class CreatePostRequest extends CreateUpdatePostRequest
{
    protected function prepareForValidation()
    {
        $this->isCreate = true;
        $this->requireOriginalImage = true;
        $this->prepareParams();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $rules = $this->baseRules();
        $rules['type'] = 'required|integer|in:0,1';
        return $rules;
    }
}
