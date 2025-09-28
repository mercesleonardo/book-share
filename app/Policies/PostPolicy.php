<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\{Post, User};

class PostPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Post $post): bool
    {
        return $post->user_id === $user->id || in_array($user->role, [UserRole::ADMIN, UserRole::MODERATOR], true);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Post $post): bool
    {
        // Post owner or Admin/Moderator (required for moderation actions)
        return $post->user_id === $user->id || in_array($user->role, [UserRole::ADMIN, UserRole::MODERATOR], true);
    }

    public function delete(User $user, Post $post): bool
    {
        // Post owner, Admin or Moderator
        return $post->user_id === $user->id || in_array($user->role, [UserRole::ADMIN, UserRole::MODERATOR], true);
    }

    public function comment(User $user, Post $post): bool
    {
        // Any authenticated user can comment (including author)
        return true;
    }

    public function rate(User $user, Post $post): bool
    {
        // Any authenticated user except the author can rate via community ratings
        return $post->user_id !== $user->id;
    }
}
