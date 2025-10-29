<?php

namespace App\Jobs;

use App\Enums\ModerationStatus;
use App\Models\Post;
use App\Notifications\PostModerationStatusChanged;
use App\Services\Moderation\OpenAIModerationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ModeratePostJob implements ShouldQueue
{
    use Dispatchable;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Post $post)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(OpenAIModerationService $moderation): void
    {
        if ($this->post->moderation_status instanceof ModerationStatus) {
            $previousStatus = $this->post->moderation_status;
        } else {
            $previousStatus = ModerationStatus::from($this->post->moderation_status);
        }

        try {
            $isSafe = $moderation->moderate($this->post->description);

            $newStatus = $isSafe
                ? ModerationStatus::Approved
                : ModerationStatus::Rejected;

            $this->post->update([
                'moderation_status' => $newStatus->value,
            ]);

            if ($previousStatus !== $newStatus) {
                $this->post->user->notify(
                    new PostModerationStatusChanged($this->post, $previousStatus, $newStatus)
                );
            }
        } catch (\Throwable $e) {
            Log::error('Moderation failed for Post ID ' . $this->post->id, [
                'message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function backoff(): array
    {
        return [10, 30, 60, 120, 300];
    }
}
