<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPassword
{
    /**
     * Build the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('パスワード再設定のお知らせ')
            ->line('パスワード再設定のご依頼をいただきました。')
            ->line('お手数ですが、下のボタンから新しいパスワードをご設定ください。')
            ->action('パスワードを再設定する', url($this->resetUrl($notifiable)))
            ->line('このリンクは :count 分間のみ有効です。', ['count' => config('auth.passwords.users.expire') / 60])
            ->line('もしこのメールに心当たりがない場合は、そのまま破棄していただいて問題ございません。');
    }
}
