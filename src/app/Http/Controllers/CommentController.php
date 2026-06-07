<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\AppNotification;
use App\Models\Content;
use App\Models\LibraryItem;

class CommentController extends Controller
{
    public function store(StoreCommentRequest $request, Content $content)
    {
        $canReview = LibraryItem::where('user_id', $request->user()->id)
            ->where('content_id', $content->id)
            ->exists();

        abort_unless($canReview && $content->user_id !== $request->user()->id, 403);

        $comment = $content->comments()->create([
            'user_id' => $request->user()->id,
            'message' => $request->input('message'),
            'is_recommended' => $request->boolean('is_recommended'),
        ]);

        $this->refreshRating($content);

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

    private function refreshRating(Content $content)
    {
        $ratingsCount = $content->comments()->count();
        $recommendedCount = $content->comments()->where('is_recommended', true)->count();

        $content->update([
            'ratings_count' => $ratingsCount,
            'rating_rate' => $ratingsCount > 0 ? (int) round($recommendedCount / $ratingsCount * 100) : 0,
        ]);
    }
}
