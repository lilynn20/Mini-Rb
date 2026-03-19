<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject('✉️ Vérifiez votre adresse email - Mini-Rb')
                ->greeting('Bonjour ' . $notifiable->name . ' !')
                ->line('Merci de vous être inscrit sur Mini-Rb.')
                ->line('Cliquez sur le bouton ci-dessous pour vérifier votre adresse email.')
                ->action('Vérifier mon email', $url)
                ->line('Si vous n\'avez pas créé de compte, ignorez cet email.');
        });
    }
}