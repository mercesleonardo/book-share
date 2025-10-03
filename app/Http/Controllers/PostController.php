<?php

namespace App\Http\Controllers;

use App\Http\Requests\{IndexPostRequest, StorePostRequest, UpdatePostRequest};
use App\Models\{Category, Post};
use App\Services\Post\{PostFilterService, PostImageService};
use Illuminate\Http\{RedirectResponse, UploadedFile};
use Illuminate\Support\Facades\{Auth};
use Illuminate\View\View;

class PostController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Post::class, 'post');
    }

    public function index(IndexPostRequest $request, PostFilterService $filter): View
    {
        $query = $filter->buildQuery($request);
        $posts = $filter->paginate($query, 15);

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
    public function store(StorePostRequest $request, PostImageService $images): RedirectResponse
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
            $data['image'] = $images->storeImage($uploaded);
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
     */
    public function update(UpdatePostRequest $request, Post $post, PostImageService $images): RedirectResponse
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
            $data['image'] = $images->storeImage($uploaded);
        }

        $post->update($data);

        if (isset($data['image']) && $oldImage && $oldImage !== $post->image) {
            $images->deleteImage($oldImage);
        }

        return redirect()->route('admin.posts.index')->with('success', __('posts.messages.updated'));
    }

    public function destroy(Post $post, PostImageService $images): RedirectResponse
    {
        $post->delete();

        if (!empty($post->image)) {
            $images->deleteImage($post->image);
        }

        return redirect()->route('admin.posts.index')->with('success', __('posts.messages.deleted'));
    }
}
