<?php

namespace App\Http\Controllers;

use App\Enums\ModerationStatus;
use App\Models\{ModerationLog, Post};
use App\Notifications\PostModerationStatusChanged;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\{Auth, Cache, Gate};

class ModerationController extends Controller
{
    public function approve(Post $post): RedirectResponse
    {
        Gate::authorize('update', $post);

        $from = $post->moderation_status?->value;
        $post->update(['moderation_status' => ModerationStatus::Approved]);
        ModerationLog::create([
            'post_id'      => $post->id,
            'moderator_id' => Auth::id(),
            'from_status'  => $from,
            'to_status'    => ModerationStatus::Approved->value,
            'note'         => null,
        ]);
        $post->user?->notify(new PostModerationStatusChanged($post, $from ?? 'pending', ModerationStatus::Approved->value));
        Cache::forget('dashboard.global_metrics');
        Cache::forget('dashboard.trend_14_days');
        Cache::forget('dashboard.top_categories');

        return back()->with('success', __('Post approved'));
    }

    public function reject(Post $post): RedirectResponse
    {
        Gate::authorize('update', $post);

        $from = $post->moderation_status?->value;
        $post->update(['moderation_status' => ModerationStatus::Rejected]);
        ModerationLog::create([
            'post_id'      => $post->id,
            'moderator_id' => Auth::id(),
            'from_status'  => $from,
            'to_status'    => ModerationStatus::Rejected->value,
            'note'         => null,
        ]);
        $post->user?->notify(new PostModerationStatusChanged($post, $from ?? 'pending', ModerationStatus::Rejected->value));
        Cache::forget('dashboard.global_metrics');
        Cache::forget('dashboard.trend_14_days');
        Cache::forget('dashboard.top_categories');

        return back()->with('success', __('Post rejected'));
    }
}
