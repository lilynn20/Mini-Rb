<?php

namespace App\Providers;

use App\Mail\VerifyEmailMail;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Mail;
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
            Mail::to($notifiable->email)->send(
                new VerifyEmailMail($url, $notifiable->name)
            );

            // Return a simple mail message to satisfy the interface
            return (new \Illuminate\Notifications\Messages\MailMessage)
                ->subject('Vérification email');
        });
    }
}