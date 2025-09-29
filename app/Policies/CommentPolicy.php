<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\{Comment, Post, User};

class CommentPolicy
{
    /**
     * Determina se o usuário pode deletar o comentário.
     * Regras:
     *  - Autor do comentário
     *  - Dono do post ao qual o comentário pertence
     *  - Admin ou Moderador
     */
    public function delete(User $user, Comment $comment): bool
    {
        $isAuthor     = $comment->user_id === $user->id;
        $isPostOwner  = $comment->post && $comment->post->user_id === $user->id;
        $isPrivileged = in_array($user->role, [UserRole::ADMIN, UserRole::MODERATOR], true);

        return $isAuthor || $isPostOwner || $isPrivileged;
    }
}
