<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Http\Requests\Posts;

use App\Http\Requests\TraitRequest;
use Illuminate\Foundation\Http\FormRequest;

class CreateUpdatePostRequest extends FormRequest
{
    use TraitRequest;
    protected $isCreate;
    protected $requireOriginalImage = false;

    protected function prepareParams()
    {
        $this->formFields = ['image', 'caption', 'place', 'tags'];
        $this->merge(array_map(function ($value) {
            if (is_string($value)) {
                return trim($value);
            } else {
                return $value;
            }
        }, $this->all()));
        $tags = $this->request->get('tags');
        if (!empty($tags) && is_array($tags)) {
            $this->merge([
                'tags' => array_map('strtolower', $tags),
            ]);
        }
    }

    public function baseRules(): array
    {
        return [
            'image' => [
                $this->isCreate ? 'required' : 'nullable',
                'image',
                'mimes:jpeg,png,jpg',
                'mimetypes:image/jpeg,image/png,image/jpg',
                'max:' . config('settings.maxKbImageUpload'),
            ],
            'original_image' => [
                $this->requireOriginalImage ? 'required' : 'nullable',
                'image',
                'mimes:jpeg,png,jpg',
                'mimetypes:image/jpeg,image/png,image/jpg',
                'max:' . config('settings.maxKbOriginalImageUpload'),
            ],
            'caption' => 'nullable|string|max:2000',
            'place' => 'sometimes|array|size:5',
            'place.place_id' => 'string|max:255',
            'place.place_name' => 'string|max:255',
            'place.place_address' => 'string|max:255',
            'place.latitude' => 'string|max:100',
            'place.longitude' => 'string|max:100',
            'tags' => 'nullable|array|max:20',
            'tags.*' => [
                'distinct',
                'string',
                'max:50',
                'regex:/^[(a-zA-Z0-9)(ぁ-んァ-ン)(一ー-龯)(\\\)(、。，,―‐_\/・～…‥：；？！＃＆＊※＠’”()°℃￥＋－×÷Ⅰ～ⅩⅠ～Ⅹ)]*$/',
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'tags.*' => 'tags',
            'place.place_id' => 'place',
            'place.place_name' => 'place',
            'place.place_address' => 'place',
            'place.latitude' => 'place',
            'place.longitude' => 'place',
        ];
    }
}
