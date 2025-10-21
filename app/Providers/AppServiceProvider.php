<?php

namespace App\Providers;

use App\Models\Alocacao;
use App\Observers\AlocacaoObserver;
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
        // Registrar o Observer para Alocacao
        Alocacao::observe(AlocacaoObserver::class);
    }
}
