<?php

namespace App\Services\Moderation;

use App\Enums\ModerationStatus;
use App\Models\{ModerationLog, Post};
use App\Notifications\PostModerationStatusChanged;
use Illuminate\Support\Facades\{Auth, Cache};

class ModerationService
{
    public function changeStatus(Post $post, ModerationStatus $to, ?string $note = null): void
    {
        $from = $post->moderation_status?->value;
        $post->update(['moderation_status' => $to]);

        ModerationLog::create([
            'post_id'      => $post->id,
            'moderator_id' => Auth::id(),
            'from_status'  => $from,
            'to_status'    => $to->value,
            'note'         => $note,
        ]);

        $post->user?->notify(new PostModerationStatusChanged($post, $from ?? 'pending', $to->value));

        Cache::forget('dashboard.global_metrics');
        Cache::forget('dashboard.trend_14_days');
        Cache::forget('dashboard.top_categories');
    }
}
