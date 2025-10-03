<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the home page with approved posts.
     */
    public function __invoke(Request $request): View
    {
        $posts = Post::with(['user:id,name', 'category:id,name'])
            ->approved()
            ->latest()
            ->paginate(12);

        return view('home', compact('posts'));
    }
}
