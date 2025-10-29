<?php

namespace App\Observers;

use App\Enums\ModerationStatus;
use App\Jobs\ModeratePostJob;
use App\Models\Post;

class PostObserver
{
    /**
     * Handle the Post "created" event.
     */
    public function created(Post $post): void
    {
        $current = $post->moderation_status;

        if ($current instanceof ModerationStatus) {
            $enum = $current;
        } elseif (is_string($current)) {
            try {
                $enum = ModerationStatus::from($current);
            } catch (\ValueError $e) {
                $enum = ModerationStatus::Pending;
            }
        } else {
            $enum = ModerationStatus::Pending;
        }

        if ($enum === ModerationStatus::Pending) {
            ModeratePostJob::dispatch($post);
        }
    }

    /**
     * Handle the Post "updated" event.
     */
    public function updated(Post $post): void
    {
        if ($post->wasChanged('title') || $post->wasChanged('book_author') || $post->wasChanged('description')) {
            ModeratePostJob::dispatch($post);

            return;
        }

        if ($post->wasChanged('moderation_status')) {
            $current = $post->moderation_status;

            if ($current instanceof ModerationStatus) {
                $enum = $current;
            } elseif (is_string($current)) {
                try {
                    $enum = ModerationStatus::from($current);
                } catch (\ValueError $e) {
                    $enum = ModerationStatus::Pending;
                }
            } else {
                $enum = ModerationStatus::Pending;
            }

            if ($enum === ModerationStatus::Pending) {
                ModeratePostJob::dispatch($post);
            }
        }
    }
}
