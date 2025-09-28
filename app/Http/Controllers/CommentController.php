<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\{Comment, Post};
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Armazena um novo comentário
     */
    public function store(StoreCommentRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Force the user_id to the authenticated user for security
        $data['user_id'] = Auth::id();

        /** @var Post $post */
        $post = Post::query()->findOrFail($data['post_id']);

        Comment::query()->create($data);

        return back()->with('status', __('comments.added'));
    }

    /**
     * Remove um comentário (autor do comentário ou dono do post)
     */
    public function destroy(Comment $comment): RedirectResponse
    {
        $user = Auth::user();

        if ($comment->user_id !== $user->id && $comment->post->user_id !== $user->id) {
            abort(403);
        }

        $comment->delete();

        return back()->with('status', __('comments.removed'));
    }
}
