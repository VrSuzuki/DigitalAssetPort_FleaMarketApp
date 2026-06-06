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
            'message' => ['required', 'string', 'max:800'],
        ];
    }

    public function attributes()
    {
        return ['message' => 'コメント'];
    }
}
