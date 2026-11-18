<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
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
        // Tugas 14: Log query untuk membuktikan eager loading & menganalisis N+1 problem
        DB::listen(function ($q) {
            logger($q->sql.' | bindings: '.json_encode($q->bindings));
        });
    }
}
