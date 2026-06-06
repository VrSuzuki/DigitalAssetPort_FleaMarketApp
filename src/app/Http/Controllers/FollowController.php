<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function toggle(Request $request, User $user)
    {
        abort_if($user->id === $request->user()->id, 403);

        $follow = Follow::where('follower_id', $request->user()->id)
            ->where('following_id', $user->id)
            ->first();

        if ($follow) {
            $follow->delete();
            $message = 'フォローを解除しました。';
        } else {
            Follow::create([
                'follower_id' => $request->user()->id,
                'following_id' => $user->id,
            ]);
            $message = 'フォローしました。';

            if ($user->notifications_enabled) {
                AppNotification::create([
                    'user_id' => $user->id,
                    'actor_id' => $request->user()->id,
                    'type' => 'follow',
                    'message' => $request->user()->display_name.'さんにフォローされました。',
                    'url' => route('profiles.show', $request->user()),
                ]);
            }
        }

        return back()->with('status', $message);
    }
}
