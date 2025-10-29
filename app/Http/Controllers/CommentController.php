<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\StoreCommentRequest;
use App\Models\{Comment, Post};
use App\Notifications\NewCommentNotification;
use App\Services\Moderation\OpenAIModerationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    protected OpenAIModerationService $moderation;

    public function __construct(OpenAIModerationService $moderation)
    {
        $this->moderation = $moderation;
    }

    /**
     * Armazena um novo comentário
     */
    public function store(StoreCommentRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $data['user_id'] = Auth::id();

        $post = Post::query()->findOrFail($data['post_id']);

        $this->authorize('comment', $post);

        $isSafe = $this->moderation->moderate($data['content']);

        if (!$isSafe) {
            return back()->with('error', __('comments.inappropriate'));
        }

        $comment = Comment::query()->create($data);

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
        $isPrivileged = in_array($acting->role, [UserRole::ADMIN, UserRole::MODERATOR], true) && !$isSelf && !$isPostOwner;

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
