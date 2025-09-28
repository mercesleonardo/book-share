<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRatingRequest;
use App\Models\{Post, Rating};
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    public function store(StoreRatingRequest $request, Post $post): RedirectResponse
    {
        // Impedir autor de usar este endpoint
        if ($post->user_id === Auth::id()) {
            return redirect()->back()->with('error', __('Você não pode avaliar sua própria publicação.'));
        }

        $data = $request->validated();

        Rating::updateOrCreate(
            ['post_id' => $post->id, 'user_id' => Auth::id()],
            ['stars' => $data['stars']]
        );

        return redirect()->route('posts.show', $post)->with('success', __('Avaliação registrada.'));
    }
}
