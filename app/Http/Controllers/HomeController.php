<?php

namespace App\Http\Controllers;

use App\Models\{Category, Post};
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the home page with approved posts.
     */
    public function __invoke(Request $request): View
    {
        $query = Post::with(['user:id,name', 'category:id,name'])
            ->approved();

        if ($request->filled('category')) {
            $query->where('category_id', (int) $request->input('category'));
        }

        if ($request->filled('q')) {
            $term = trim((string) $request->input('q'));
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', "%{$term}%")
                    ->orWhere('book_author', 'like', "%{$term}%");
            });
        }

        $posts      = $query->latest()->paginate(12)->withQueryString();
        $categories = Category::orderBy('name')->get(['id', 'name']);

        return view('home', compact('posts', 'categories'));
    }
}
