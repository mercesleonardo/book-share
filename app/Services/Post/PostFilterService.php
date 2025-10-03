<?php

namespace App\Services\Post;

use App\Enums\UserRole;
use App\Http\Requests\IndexPostRequest;
use App\Models\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PostFilterService
{
    public function buildQuery(IndexPostRequest $request): Builder
    {
        $validated = $request->validated();

        $query = Post::query()
            ->with(['category', 'user'])
            ->withAvg('ratings', 'stars')
            ->withCount('ratings');

        $hasFilters = !empty($validated['category']) || !empty($validated['user']) || !empty($validated['book_author']) || !empty($validated['q']) || !empty($validated['status']);

        $isPrivileged = in_array(Auth::user()->role, [UserRole::ADMIN, UserRole::MODERATOR], true);

        if (!$isPrivileged) {
            $onlyUserFilter = !empty($validated['user']) && empty($validated['category']) && empty($validated['book_author']) && empty($validated['q']) && empty($validated['status']);
            $forcingForeign = $onlyUserFilter && (int) $validated['user'] !== Auth::id();

            if (!$hasFilters || $forcingForeign) {
                $query->where('user_id', Auth::id());

                if ($forcingForeign) {
                    unset($validated['user']);
                }
            }
        }

        if (!empty($validated['category'])) {
            $query->where('category_id', $validated['category']);
        }

        if (!empty($validated['user'])) {
            $query->where('user_id', $validated['user']);
        }

        if (!empty($validated['book_author'])) {
            $authorName = $validated['book_author'];
            $query->where('book_author', 'like', "%{$authorName}%");
        }

        if (!empty($validated['q'])) {
            $q = $validated['q'];
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('book_author', 'like', "%{$q}%");
            });
        }

        if (!empty($validated['status']) && in_array(Auth::user()->role, [UserRole::ADMIN, UserRole::MODERATOR], true)) {
            $query->where('moderation_status', $validated['status']);
        }

        return $query;
    }

    public function paginate(Builder $query, int $perPage = 15): LengthAwarePaginator
    {
        return $query->orderByDesc('id')->paginate($perPage)->withQueryString();
    }
}
