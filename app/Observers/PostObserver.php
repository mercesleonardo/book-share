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
        // Garantir que temos uma instância do enum (defensivo: fallback para Pending)
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

        // Somente despachar se estiver pendente
        if ($enum === ModerationStatus::Pending) {
            ModeratePostJob::dispatch($post);
        }
    }
}
