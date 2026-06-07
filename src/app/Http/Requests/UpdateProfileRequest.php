<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user() !== null;
    }

    public function rules()
    {
        return [
            'handle' => [
                'required',
                'alpha_dash',
                'min:3',
                'max:30',
                Rule::unique('users', 'handle')->ignore($this->user()->id),
            ],
            'nickname' => ['required', 'string', 'max:40'],
            'avatar' => ['nullable', 'image', 'max:4096'],
            'bio' => ['nullable', 'string', 'max:1500'],
        ];
    }

    public function attributes()
    {
        return [
            'handle' => 'ユーザーID',
            'nickname' => 'ニックネーム',
            'avatar' => 'プロフィールアイコン',
            'bio' => '自己紹介文',
        ];
    }
}
