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

        if (!empty($validated['q'])) {
            $search = $validated['q'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $categories = $query->orderBy('name')->paginate(15)->withQueryString();

        // Always provide the full list for the select so options are not restricted by filters
        $allCategories = Category::orderBy('name')->get(['id', 'name']);

        return view('categories.index', compact('categories', 'allCategories'));
    }

    public function create(): View
    {
        return view('categories.create');
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $category = Category::create($request->validated());

        return redirect()->route('admin.categories.index')->with('success', __('categories.messages.created'));
    }

    public function edit(Category $category): View
    {
        return view('categories.edit', compact('category'));
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $category->update($request->validated());

        return redirect()->route('admin.categories.index')->with('success', __('categories.messages.updated'));
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', __('categories.messages.deleted'));
    }
}
