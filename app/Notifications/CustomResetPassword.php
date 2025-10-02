<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class CustomResetPassword extends ResetPassword
{
    public function toMail($notifiable)
    {
        $url = $this->resetUrl($notifiable); // Laravel 11; untuk 10 ada cara sedikit beda

        return (new MailMessage)
            ->subject('Reset Kata Sandi - Portal Inovasi')
            ->greeting('Halo!')
            ->line('Anda menerima email ini karena kami menerima permintaan reset kata sandi untuk akun Anda.')
            ->action('Atur Ulang Kata Sandi', $url)
            ->line('Tautan reset berlaku selama 60 menit.')
            ->line('Jika Anda tidak meminta reset, abaikan email ini.');
    }
}
