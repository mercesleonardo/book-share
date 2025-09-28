<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\{IndexPostRequest, StorePostRequest, UpdatePostRequest};
use App\Models\{Category, Post};
use Illuminate\Http\{RedirectResponse, UploadedFile};
use Illuminate\Support\Facades\{Auth, Storage};
use Illuminate\View\View;

class PostController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Post::class, 'post');
    }

    public function index(IndexPostRequest $request): View
    {
        $validated = $request->validated();

        $query = Post::query()->with(['category', 'user']);

        $hasFilters = !empty($validated['category']) || !empty($validated['user']) || !empty($validated['author']) || !empty($validated['q']) || !empty($validated['status']);

        $isPrivileged = in_array(Auth::user()->role, [UserRole::ADMIN, UserRole::MODERATOR], true);

        // Combined policy:
        // - Basic user with no filters => only their own posts.
        // - Basic user attempting only ?user=another_id (no other filters) => ignore and show only their posts.
        // - If any other filter present (category, q, status - status still ignored for basic), allow wide visibility.
        if (!$isPrivileged) {
            $onlyUserFilter = !empty($validated['user']) && empty($validated['category']) && empty($validated['author']) && empty($validated['q']) && empty($validated['status']);
            $forcingForeign = $onlyUserFilter && (int)$validated['user'] !== Auth::id();

            if (!$hasFilters || $forcingForeign) {
                // Restrict visibility
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

        // Filtro de autor do livro (campo textual 'author') agora separado
        if (!empty($validated['author'])) {
            $authorName = $validated['author'];
            $query->where('author', 'like', "%{$authorName}%");
        }

        if (!empty($validated['q'])) {
            $q = $validated['q'];
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('author', 'like', "%{$q}%");
            });
        }

        // Filtro de status apenas para admin/mod
        if (!empty($validated['status']) && in_array(Auth::user()->role, [UserRole::ADMIN, UserRole::MODERATOR], true)) {
            $query->where('moderation_status', $validated['status']);
        }

        $posts = $query->orderByDesc('id')->paginate(15)->withQueryString();

        $categories = Category::orderBy('name')->get(['id', 'name']);
        $users      = \App\Models\User::orderBy('name')->get(['id', 'name']);

        return view('posts.index', compact('posts', 'categories', 'users'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('posts.create', compact('categories'));
    }

    /**
     * @param StorePostRequest $request
     */
    public function store(StorePostRequest $request): RedirectResponse
    {
        $data            = $request->validated();
        $data['user_id'] = Auth::id();
        // Fallback if not provided
        $data['author'] = $data['author'] ?? Auth::user()->name;

        if ($request->hasFile('image')) {
            /** @var UploadedFile $uploaded */
            $uploaded      = $request->file('image');
            $data['image'] = $uploaded->store('posts', 'public');
        }
        Post::create($data);

        return redirect()->route('posts.index')->with('success', __('posts.messages.created'));
    }

    public function edit(Post $post): View
    {
        $categories = Category::orderBy('name')->get();

        return view('posts.edit', compact('post', 'categories'));
    }

    public function show(Post $post): View
    {
        $previous = Post::select(['id', 'title', 'slug'])
            ->byAuthor($post->user_id)
            ->where('id', '<', $post->id)
            ->orderByDesc('id')
            ->first();

        $next = Post::select(['id', 'title', 'slug'])
            ->byAuthor($post->user_id)
            ->where('id', '>', $post->id)
            ->orderBy('id')
            ->first();

        $related = collect();

        $related = Post::select(['id', 'title', 'slug'])
            ->where('user_id', $post->user_id)
            ->where('id', '!=', $post->id)
            ->latest()
            ->limit(5)
            ->get();

        return view('posts.show', [
            'post'     => $post,
            'previous' => $previous,
            'next'     => $next,
            'related'  => $related,
        ]);
    }

    /**
     * @param UpdatePostRequest $request
     */
    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        $data = $request->validated();
        // Ensure fallback without relying on the key presence
        $data['author'] = $data['author'] ?? Auth::user()->name;
        $oldImage       = $post->image;

        if ($request->hasFile('image')) {
            /** @var UploadedFile $uploaded */
            $uploaded      = $request->file('image');
            $data['image'] = $uploaded->store('posts', 'public');
        }

        $post->update($data);

        if (isset($data['image']) && $oldImage && $oldImage !== $post->image) {
            Storage::disk('public')->delete($oldImage);
        }

        return redirect()->route('posts.index')->with('success', __('posts.messages.updated'));
    }

    public function destroy(Post $post): RedirectResponse
    {
        $post->delete();

        if (!empty($post->image)) {
            Storage::disk('public')->delete($post->image);
        }

        return redirect()->route('posts.index')->with('success', __('posts.messages.deleted'));
    }
}
