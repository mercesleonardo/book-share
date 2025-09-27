<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexCategoryRequest;
use App\Http\Requests\{StoreCategoryRequest, UpdateCategoryRequest};
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Category::class, 'category');
    }

    public function index(IndexCategoryRequest $request): View
    {
        $validated = $request->validated();

        $query = Category::query();

        if (!empty($validated['category'])) {
            $query->where('id', $validated['category']);
        }

        $categories = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('categories.create');
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $category = Category::create($request->validated());

        return redirect()->route('categories.index')->with('success', __('categories.messages.created'));
    }

    public function edit(Category $category): View
    {
        return view('categories.edit', compact('category'));
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $category->update($request->validated());

        return redirect()->route('categories.index')->with('success', __('categories.messages.updated'));
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();

        return redirect()->route('categories.index')->with('success', __('categories.messages.deleted'));
    }
}
