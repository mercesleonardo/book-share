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
            ->subject(__('Set up your account password'))
            ->line(__('An administrator created an account for you. Click the button below to define your password and activate access.'))
            ->action(__('Define Password'), $url)
            ->line(__('If you did not expect this email, you can ignore it.'));
    }
}
