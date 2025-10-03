<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class InitialPasswordSetupNotification extends BaseResetPassword
{
    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $url = $this->resetUrl($notifiable);

        return (new MailMessage())
            ->subject(__('notifications.password.setup_subject'))
            ->line(__('notifications.password.setup_line'))
            ->action(__('notifications.password.setup_action'), $url)
            ->line(__('notifications.password.ignore'));
    }
}
