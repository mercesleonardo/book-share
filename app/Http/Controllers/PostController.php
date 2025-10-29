<?php

namespace App\Http\Controllers;

use App\Enums\ModerationStatus;
use App\Http\Requests\{IndexPostRequest, StorePostRequest, UpdatePostRequest};
use App\Models\{Category, Post};
use App\Models\User;
use App\Notifications\PostCreatedNotification;
use App\Services\Moderation\OpenAIModerationService;
use App\Services\Post\{PostFilterService, PostImageService};
use Illuminate\Http\{RedirectResponse};
use Illuminate\Support\Facades\{Auth};
use Illuminate\View\View;

class PostController extends Controller
{
    protected OpenAIModerationService $moderation;

    public function __construct(OpenAIModerationService $moderation)
    {
        $this->authorizeResource(Post::class, 'post');
        $this->moderation = $moderation;
    }

    public function index(IndexPostRequest $request, PostFilterService $filter): View
    {
        $query = $filter->buildQuery($request);
        $posts = $filter->paginate($query, 15);

        $categories = Category::orderBy('name')->get(['id', 'name']);
        $users      = User::orderBy('name')->get(['id', 'name']);

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
        $user = Auth::user();

        $data                      = $request->validated();
        $data['user_id']           = $user->id;
        $data['book_author']       = $data['book_author'] ?? $user->name;
        $data['user_rating']       = $data['user_rating'] ?? null;
        $data['moderation_status'] = ModerationStatus::Pending->value;

        if ($request->hasFile('image')) {
            $data['image'] = $images->storeImage($request->file('image'));
        }

        $post = Post::create($data);

        $user->notify(new PostCreatedNotification($post));

        return redirect()
            ->route('admin.posts.index')
            ->with('warning', __('posts.messages.under_review', [
                'title' => $post->title,
            ]));
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
        $data                      = $request->validated();
        $data['book_author']       = $data['book_author'] ?? Auth::user()->name;
        $data['user_rating']       = $post->user_rating ?? null;
        $oldImage                  = $post->image;
        $data['moderation_status'] = ModerationStatus::Pending->value;

        if ($request->hasFile('image')) {
            $data['image'] = $images->storeImage($request->file('image'));
        }

        $post->update($data);

        if (isset($data['image']) && $oldImage && $oldImage !== $post->image) {
            $images->deleteImage($oldImage);
        }

        return redirect()
            ->route('admin.posts.index')
            ->with('warning', __('posts.messages.under_review'));
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
