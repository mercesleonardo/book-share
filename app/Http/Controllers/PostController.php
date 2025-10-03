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

        $query = Post::query()
            ->with(['category', 'user'])
            ->withAvg('ratings', 'stars')
            ->withCount('ratings');

        $hasFilters = !empty($validated['category']) || !empty($validated['user']) || !empty($validated['book_author']) || !empty($validated['q']) || !empty($validated['status']);

        $isPrivileged = in_array(Auth::user()->role, [UserRole::ADMIN, UserRole::MODERATOR], true);

        // Combined policy:
        // - Basic user with no filters => only their own posts.
        // - Basic user attempting only ?user=another_id (no other filters) => ignore and show only their posts.
        // - If any other filter present (category, q, status - status still ignored for basic), allow wide visibility.
        if (!$isPrivileged) {
            $onlyUserFilter = !empty($validated['user']) && empty($validated['category']) && empty($validated['book_author']) && empty($validated['q']) && empty($validated['status']);
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

        // Filtro de autor do livro (campo textual 'book_author')
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
     * Store a newly created Post.
     *
     * @param StorePostRequest $request
     */
    public function store(StorePostRequest $request): RedirectResponse
    {
        $data            = $request->validated();
        $data['user_id'] = Auth::id();
        // Fallback if not provided
        $data['book_author'] = $data['book_author'] ?? Auth::user()->name;

        // Se não vier user_rating, define um padrão (ex: 5) ou deixa null
        if (!array_key_exists('user_rating', $data)) {
            $data['user_rating'] = null; // autor pode avaliar depois
        }

        /** @var \Illuminate\Http\Request $request */
        if ($request->hasFile('image')) {
            /** @var UploadedFile $uploaded */
            $uploaded      = $request->file('image');
            $data['image'] = $uploaded->store('posts', 'public');
        }
        Post::create($data);

        return redirect()->route('admin.posts.index')->with('success', __('posts.messages.created'));
    }

    public function edit(Post $post): View
    {
        $categories = Category::orderBy('name')->get();

        return view('posts.edit', compact('post', 'categories'));
    }

    public function show(Post $post): View
    {
        $post->loadMissing([
            'ratings.user:id,name',
            'comments.user:id,name',
            'category:id,name',
            'user:id,name',
        ]);

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
     * Update the specified Post.
     *
     * @param UpdatePostRequest $request
     * @param Post $post
     */
    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        $data = $request->validated();
        // Ensure fallback without relying on the key presence
        $data['book_author'] = $data['book_author'] ?? Auth::user()->name;

        if (!array_key_exists('user_rating', $data)) {
            $data['user_rating'] = $post->user_rating; // mantém existente
        }
        $oldImage = $post->image;

        /** @var \Illuminate\Http\Request $request */
        if ($request->hasFile('image')) {
            /** @var UploadedFile $uploaded */
            $uploaded      = $request->file('image');
            $data['image'] = $uploaded->store('posts', 'public');
        }

        $post->update($data);

        if (isset($data['image']) && $oldImage && $oldImage !== $post->image) {
            Storage::disk('public')->delete($oldImage);
        }

        return redirect()->route('admin.posts.index')->with('success', __('posts.messages.updated'));
    }

    public function destroy(Post $post): RedirectResponse
    {
        $post->delete();

        if (!empty($post->image)) {
            Storage::disk('public')->delete($post->image);
        }

        return redirect()->route('admin.posts.index')->with('success', __('posts.messages.deleted'));
    }
}
