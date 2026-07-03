<?php

namespace App\Providers;

use App\Models\AnnonceImage;
use App\Observers\AnnonceImageObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        AnnonceImage::observe(AnnonceImageObserver::class);
    }
}