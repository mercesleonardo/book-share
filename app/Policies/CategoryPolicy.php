<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\{Category, User};

class CategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [UserRole::ADMIN, UserRole::MODERATOR], true);
    }

    public function view(User $user, Category $category): bool
    {
        return in_array($user->role, [UserRole::ADMIN, UserRole::MODERATOR], true);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, [UserRole::ADMIN, UserRole::MODERATOR], true);
    }

    public function update(User $user, Category $category): bool
    {
        return in_array($user->role, [UserRole::ADMIN, UserRole::MODERATOR], true);
    }

    public function delete(User $user, Category $category): bool
    {
        return in_array($user->role, [UserRole::ADMIN, UserRole::MODERATOR], true);
    }
}
