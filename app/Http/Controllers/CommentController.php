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

        return back()->with('success', __('comments.added'));
    }

    /**
     * Remove um comentário (autor do comentário ou dono do post)
     */
    public function destroy(Comment $comment): RedirectResponse
    {
        // Garantir que a relação post esteja carregada para evitar lazy loading na policy
        $comment->loadMissing('post');
        $this->authorize('delete', $comment);

        $acting       = Auth::user();
        $isSelf       = $comment->user_id === $acting->id;
        $isPostOwner  = $comment->post->user_id === $acting->id && !$isSelf;
        $isPrivileged = in_array($acting->role, [\App\Enums\UserRole::ADMIN, \App\Enums\UserRole::MODERATOR], true) && !$isSelf && !$isPostOwner;

        $comment->delete();

        $message = __('comments.removed');

        if ($isSelf) {
            $message = __('comments.removed_self');
        } elseif ($isPostOwner) {
            $message = __('comments.removed_as_post_owner');
        } elseif ($isPrivileged) {
            $message = __('comments.removed_as_moderator');
        }

        return back()->with('success', $message);
    }
}
