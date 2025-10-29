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

        // Garantir que temos instâncias de ModerationStatus ao notificar
        $current = $post->moderation_status;

        if ($current instanceof ModerationStatus) {
            $fromEnum = $current;
        } elseif ($current !== null) {
            $fromEnum = ModerationStatus::from($current);
        } else {
            $fromEnum = ModerationStatus::Pending;
        }

        // Persistir o novo status usando o value do enum para compatibilidade
        $post->update(['moderation_status' => $to->value]);

        ModerationLog::create([
            'post_id'      => $post->id,
            'moderator_id' => Auth::id(),
            'from_status'  => $fromEnum->value,
            'to_status'    => $to->value,
            'note'         => $note,
        ]);

        // Passar instâncias de ModerationStatus para a notificação (assinatura exige isso)
        $post->user?->notify(new PostModerationStatusChanged($post, $fromEnum, $to));

        Cache::forget('dashboard.global_metrics');
        Cache::forget('dashboard.trend_14_days');
        Cache::forget('dashboard.top_categories');
    }
}
