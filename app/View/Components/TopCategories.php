<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class TopCategories extends Component
{
    /**
     * @param Collection<int, mixed> $rows  Expect collection of objects with ->category (optional) and ->total
     */
    public function __construct(public Collection $rows)
    {
    }

    public function render(): View|Closure|string
    {
        return view('components.top-categories');
    }
}
