<?php

namespace App\Notifications;

use App\Models\{Comment, Post};
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewCommentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Comment $comment)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        /** @var Post $post */
        $post = $this->comment->post;

        return (new MailMessage())
            ->subject(__('notifications.comments.new_subject'))
            ->greeting(__('notifications.greeting', ['name' => $notifiable->name]))
            ->line(__('notifications.comments.new_line', ['title' => $post->title]))
            ->line($this->comment->content)
            ->action(__('notifications.view_post'), route('admin.posts.show', $post))
            ->line(__('notifications.thanks'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'post_id'    => $this->comment->post_id,
            'comment_id' => $this->comment->id,
            'excerpt'    => str()->limit($this->comment->content, 120),
        ];
    }
}
