<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PostPublicController extends Controller
{
    /**
     * Display the specified approved post.
     */
    public function show(Post $post): View
    {
        // Verificar se o post está aprovado
        if ($post->moderation_status !== \App\Enums\ModerationStatus::Approved) {
            abort(404);
        }

        // Carregar relacionamentos necessários
        $post->load([
            'user:id,name',
            'category:id,name',
            'comments.user:id,name',
            'ratings' => function ($query) {
                if (Auth::check()) {
                    $query->where('user_id', Auth::id());
                }
            },
        ]);
        $post->loadAvg('ratings', 'stars');
        $post->loadCount('ratings');

        // Buscar posts relacionados da mesma categoria (apenas aprovados)
        $related = Post::query()
            ->approved()
            ->where('category_id', $post->category_id)
            ->where('id', '!=', $post->id)
            ->with(['user:id,name', 'category:id,name'])
            ->limit(3)
            ->latest()
            ->get();

        // Buscar posts anterior e próximo (apenas aprovados)
        $previous = Post::query()
            ->approved()
            ->where('id', '<', $post->id)
            ->orderBy('id', 'desc')
            ->first();

        $next = Post::query()
            ->approved()
            ->where('id', '>', $post->id)
            ->orderBy('id', 'asc')
            ->first();

        return view('posts.public-show', compact('post', 'related', 'previous', 'next'));
    }
}
