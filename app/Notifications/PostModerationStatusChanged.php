<?php

namespace App\Notifications;

use App\Enums\ModerationStatus;
use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PostModerationStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Post $post, public ModerationStatus $from, public ModerationStatus $to)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $fromLabel = __('moderation.' . $this->from->value);
        $toLabel   = __('moderation.' . $this->to->value);

        return (new MailMessage())
            ->subject(__('notifications.moderation.changed_subject', ['from' => $fromLabel, 'to' => $toLabel]))
            ->line(__('notifications.moderation.changed_line', ['from' => $fromLabel, 'to' => $toLabel]))
            ->action(__('notifications.view'), route('admin.posts.show', $this->post));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'post_id' => $this->post->id,
            'from'    => $this->from->value,
            'to'      => $this->to->value,
            'title'   => $this->post->title,
        ];
    }
}
