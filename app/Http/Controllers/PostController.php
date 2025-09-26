<?php

namespace App\Http\Controllers;

use App\Http\Requests\{StorePostRequest, UpdatePostRequest};
use App\Models\{Category, Post};
use Illuminate\Http\{RedirectResponse, UploadedFile};
use Illuminate\View\View;

class PostController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Post::class, 'post');
    }

    public function index(): View
    {
        $posts = Post::query()->with(['category', 'user'])->orderByDesc('id')->paginate(15);

        return view('posts.index', compact('posts'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('posts.create', compact('categories'));
    }

    public function store(StorePostRequest $request): RedirectResponse
    {
        $data            = $request->validated();
        $data['user_id'] = auth()->id();

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

    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            /** @var UploadedFile $uploaded */
            $uploaded      = $request->file('image');
            $data['image'] = $uploaded->store('posts', 'public');
        }
        $post->update($data);

        return redirect()->route('posts.index')->with('success', __('posts.messages.updated'));
    }

    public function destroy(Post $post): RedirectResponse
    {
        $post->delete();

        return redirect()->route('posts.index')->with('success', __('posts.messages.deleted'));
    }
}
