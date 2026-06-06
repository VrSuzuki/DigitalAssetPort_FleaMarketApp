<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AccountSettingsRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user() !== null;
    }

    public function rules()
    {
        return [
            'notifications_enabled' => ['nullable', 'boolean'],
            'show_following_count' => ['nullable', 'boolean'],
            'show_follower_count' => ['nullable', 'boolean'],
        ];
    }
}
