<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Http\Requests\Users;

use App\Http\Requests\TraitRequest;
use Illuminate\Foundation\Http\FormRequest;

class SettingNotificationRequest extends FormRequest
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
            'enable_notification_post' => 'nullable|integer|in:0,1',
            'enable_notification_comment' => 'nullable|integer|in:0,1',
            'enable_notification_following' => 'nullable|integer|in:0,1',
        ];
    }
}
