<?php

namespace App\View\Components;

use App\Models\Post;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class LatestPostsCarousel extends Component
{
    /**
     * Coleção das últimas postagens aprovadas.
     * @var \Illuminate\Support\Collection<int, Post>
     */
    public $posts;

    public function __construct(public int $limit = 5)
    {
        $this->posts = Post::query()
            ->approved()
            ->latest('created_at')
            ->limit($limit)
            ->with(['user:id,name', 'category:id,name'])
            ->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.latest-posts-carousel');
    }
}
