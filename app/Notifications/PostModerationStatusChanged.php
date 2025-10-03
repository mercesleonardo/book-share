<?php

namespace App\Notifications;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PostModerationStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Post $post, public string $from, public string $to)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject(__('notifications.moderation.changed_subject', ['from' => $this->from, 'to' => $this->to]))
            ->line(__('notifications.moderation.changed_line', ['from' => $this->from, 'to' => $this->to]))
            ->action(__('notifications.view'), route('admin.posts.show', $this->post));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'post_id' => $this->post->id,
            'from'    => $this->from,
            'to'      => $this->to,
            'title'   => $this->post->title,
        ];
    }
}
