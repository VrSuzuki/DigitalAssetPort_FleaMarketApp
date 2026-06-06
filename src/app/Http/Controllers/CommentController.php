<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\AppNotification;
use App\Models\Content;

class CommentController extends Controller
{
    public function store(StoreCommentRequest $request, Content $content)
    {
        $comment = $content->comments()->create([
            'user_id' => $request->user()->id,
            'message' => $request->input('message'),
        ]);

        if ($content->user_id !== $request->user()->id && $content->author->notifications_enabled) {
            AppNotification::create([
                'user_id' => $content->user_id,
                'actor_id' => $request->user()->id,
                'type' => 'comment',
                'message' => $request->user()->display_name.'さんが「'.$content->title.'」にコメントしました。',
                'url' => route('contents.show', $content).'#comment-'.$comment->id,
            ]);
        }

        return back()->with('status', 'コメントを投稿しました。');
    }
}
