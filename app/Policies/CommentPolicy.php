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
        $isAuthor = $comment->user_id === $user->id;
        // Evita lazy loading: se a relação não estiver carregada, use post_id comparando com o user_id do post via atributo resolvido.
        // Idealmente o Comment é carregado com 'post.user_id'; se não, fazemos uma checagem leve sem forçar load.
        $isPostOwner = false;

        if ($comment->relationLoaded('post') && $comment->post) {
            $isPostOwner = $comment->post->user_id === $user->id;
        } else {
            // fallback: consultar somente user_id do post (single column) sem carregar modelo inteiro
            $postUserId = Comment::query()
                ->whereKey($comment->getKey())
                ->join('posts', 'posts.id', '=', 'comments.post_id')
                ->value('posts.user_id');
            $isPostOwner = (int) $postUserId === $user->id;
        }
        $isPrivileged = in_array($user->role, [UserRole::ADMIN, UserRole::MODERATOR], true);

        return $isAuthor || $isPostOwner || $isPrivileged;
    }
}
