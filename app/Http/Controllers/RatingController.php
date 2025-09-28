<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRatingRequest;
use App\Models\{Post, Rating};
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\{Auth, Cache};

class RatingController extends Controller
{
    public function store(StoreRatingRequest $request, Post $post): RedirectResponse
    {
        // Impedir autor de usar este endpoint
        if ($post->user_id === Auth::id()) {
            return redirect()->back()->with('error', __('posts.messages.rating_self_forbidden'));
        }

        $data = $request->validated();

        $existing = Rating::where('post_id', $post->id)->where('user_id', Auth::id())->first();

        Rating::updateOrCreate(
            ['post_id' => $post->id, 'user_id' => Auth::id()],
            ['stars' => $data['stars']]
        );

        // Invalidar cache simples (sem tags) das métricas deste post
        Cache::forget('post:ratings:avg:' . $post->id);
        Cache::forget('post:ratings:count:' . $post->id);

        $messageKey = $existing ? 'posts.messages.rating_updated' : 'posts.messages.rating_saved';

        return redirect()->route('posts.show', $post)->with('success', __($messageKey));
    }
}
