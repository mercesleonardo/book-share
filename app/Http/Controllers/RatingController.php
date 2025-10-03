<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRatingRequest;
use App\Models\Post;
use App\Services\Rating\RatingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    public function store(StoreRatingRequest $request, Post $post, RatingService $ratings): RedirectResponse
    {
        $this->authorize('rate', $post);

        // Impedir autor de usar este endpoint
        if ($post->user_id === Auth::id()) {
            return redirect()->back()->with('error', __('posts.messages.rating_self_forbidden'));
        }

        $data       = $request->validated();
        $existing   = $ratings->set($post, Auth::id(), (int) $data['stars']);
        $messageKey = $existing ? 'posts.messages.rating_updated' : 'posts.messages.rating_saved';

        return redirect()->back()->with('success', __($messageKey));
    }
}
