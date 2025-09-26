<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use Illuminate\View\View;
use App\Models\{Category, Post};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\{RedirectResponse, UploadedFile};
use App\Http\Requests\{StorePostRequest, UpdatePostRequest, IndexPostRequest};

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

        // Users with basic role can only see their own posts
        if (! in_array(Auth::user()->role, [UserRole::ADMIN, UserRole::MODERATOR], true)) {
            $query->where('user_id', Auth::id());
            // Removed any malicious user filter sent
            unset($validated['user']);
        }

        if (!empty($validated['category'])) {
            $query->where('category_id', $validated['category']);
        }
        if (!empty($validated['user'])) {
            $query->where('user_id', $validated['user']);
        }
        if (!empty($validated['q'])) {
            $q = $validated['q'];
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('author', 'like', "%{$q}%");
            });
        }

        $posts = $query->orderByDesc('id')->paginate(15)->withQueryString();

        $categories = Category::orderBy('name')->get(['id', 'name']);
        $users    = \App\Models\User::orderBy('name')->get(['id', 'name']);

        return view('posts.index', compact('posts', 'categories', 'users'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('posts.create', compact('categories'));
    }

    public function store(StorePostRequest $request): RedirectResponse
    {
    $data            = $request->validated();
    $data['user_id'] = Auth::id();

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
        return view('posts.show', compact('post'));
    }

    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        $data     = $request->validated();
        $oldImage = $post->image;

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
