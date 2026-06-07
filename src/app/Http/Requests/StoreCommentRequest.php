<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user() !== null;
    }

    public function rules()
    {
        return [
            'is_recommended' => ['required', 'boolean'],
            'message' => ['required', 'string', 'max:800'],
        ];
    }

    public function attributes()
    {
        return [
            'is_recommended' => 'おすすめ評価',
            'message' => 'コメント',
        ];
    }
}
