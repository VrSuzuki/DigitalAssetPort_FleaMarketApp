<?php

return [
    'required' => ':attributeを入力してください。',
    'email' => ':attributeはメールアドレス形式で入力してください。',
    'confirmed' => ':attributeの確認入力が一致しません。',
    'unique' => 'この:attributeはすでに使われています。',
    'alpha_dash' => ':attributeは半角英数字、ハイフン、アンダースコアで入力してください。',
    'string' => ':attributeは文字列で入力してください。',
    'integer' => ':attributeは整数で入力してください。',
    'boolean' => ':attributeの値が正しくありません。',
    'image' => ':attributeには画像ファイルを指定してください。',
    'file' => ':attributeにはファイルを指定してください。',
    'exists' => '選択した:attributeが正しくありません。',
    'min' => [
        'numeric' => ':attributeは:min以上で入力してください。',
        'file' => ':attributeは:minKB以上にしてください。',
        'string' => ':attributeは:min文字以上で入力してください。',
        'array' => ':attributeは:min件以上選択してください。',
    ],
    'max' => [
        'numeric' => ':attributeは:max以下で入力してください。',
        'file' => ':attributeは:maxKB以下にしてください。',
        'string' => ':attributeは:max文字以内で入力してください。',
        'array' => ':attributeは:max件以内にしてください。',
    ],
    'attributes' => [
        'handle' => 'ユーザーID',
        'email' => 'メールアドレス',
        'password' => 'パスワード',
        'title' => 'コンテンツ名',
        'message' => 'コメント',
        'is_recommended' => 'おすすめ評価',
    ],
];
