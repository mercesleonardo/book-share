<?php

namespace App\Http\Controllers;

use App\Enums\ModerationStatus;
use App\Models\Post;
use App\Services\Moderation\ModerationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class ModerationController extends Controller
{
    public function approve(Post $post, ModerationService $moderation): RedirectResponse
    {
        Gate::authorize('update', $post);
        $moderation->changeStatus($post, ModerationStatus::Approved);

        return back()->with('success', __('Post approved'));
    }

    public function reject(Post $post, ModerationService $moderation): RedirectResponse
    {
        Gate::authorize('update', $post);
        $moderation->changeStatus($post, ModerationStatus::Rejected);

        return back()->with('success', __('Post rejected'));
    }
}
