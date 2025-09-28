<?php

namespace App\Notifications;

use App\Models\{Comment, Post};
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewCommentNotification extends Notification
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
            ->subject(__('New comment on your post'))
            ->greeting(__('Hello :name', ['name' => $notifiable->name]))
            ->line(__('Your post ":title" received a new comment.', ['title' => $post->title]))
            ->line($this->comment->content)
            ->action(__('View post'), route('posts.show', $post))
            ->line(__('Thank you for using our application!'));
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
