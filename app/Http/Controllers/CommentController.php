<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\{Comment, Post};
use App\Notifications\NewCommentNotification;
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

        $this->authorize('comment', $post);

        $comment = Comment::query()->create($data);

        // Notificar autor do post, exceto se ele mesmo comentou
        if ($post->user_id !== $comment->user_id) {
            $post->user->notify(new NewCommentNotification($comment));
        }

        return back()->with('status', __('comments.added'));
    }

    /**
     * Remove um comentário (autor do comentário ou dono do post)
     */
    public function destroy(Comment $comment): RedirectResponse
    {
        $user = Auth::user();

        $isOwner      = $comment->user_id === $user->id;
        $isPostAuthor = $comment->post->user_id === $user->id;
        $isPrivileged = in_array($user->role->value ?? $user->role, ['admin', 'moderator'], true);

        if (!$isOwner && !$isPostAuthor && !$isPrivileged) {
            abort(403);
        }

        $comment->delete();

        return back()->with('status', __('comments.removed'));
    }
}
