<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;
use Symfony\Component\Console\Terminal;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        config(['app.locale' => 'id']);
        Carbon::setLocale('id');

        // 👇 Tambahan untuk bypass error stty di shared hosting
        if (class_exists(Terminal::class)) {
            try {
                Terminal::setSttyAvailable(false);
            } catch (\Throwable $e) {
                // Abaikan kalau method tidak ada (versi symfony lama)
            }
        }
    }
}
