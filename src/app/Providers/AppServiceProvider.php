<?php

namespace App\Providers;

use App\Events\ContactCreated;
use App\Listeners\AITryAnswerToContact;
use App\Listeners\SendContactCreatedEmail;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
    }

}
