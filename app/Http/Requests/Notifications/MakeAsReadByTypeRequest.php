<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Http\Requests\Notifications;

use App\Enums\Notifications\NotificationTypeEnum;
use App\Http\Requests\TraitRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MakeAsReadByTypeRequest extends FormRequest
{
    use TraitRequest;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'type' => [
                'required',
                'string',
                Rule::in([
                    NotificationTypeEnum::FOLLOW_REQUEST,
                    NotificationTypeEnum::ACTIVITY
                ])
            ],
        ];
    }
}
