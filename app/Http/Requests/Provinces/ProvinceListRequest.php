<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Http\Requests\Provinces;

use App\Http\Requests\TraitRequest;
use Illuminate\Foundation\Http\FormRequest;

class ProvinceListRequest extends FormRequest
{
    use TraitRequest;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'lang_code' => 'required',
        ];
    }
}
