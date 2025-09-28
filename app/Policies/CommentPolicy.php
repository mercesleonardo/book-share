<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\{Comment, User};

class CommentPolicy
{
    public function delete(User $user, Comment $comment): bool
    {
        return $comment->user_id === $user->id
            || $comment->post->user_id === $user->id
            || in_array($user->role, [UserRole::ADMIN, UserRole::MODERATOR], true);
    }
}
