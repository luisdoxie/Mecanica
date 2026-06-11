<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Carbon::setLocale('es');
        date_default_timezone_set('America/La_Paz');

        // Las vistas gerente usan el layout de admin cuando el usuario es SUPER_ADMIN
        View::composer('gerente.*', function ($view) {
            $layout = (Auth::check() && Auth::user()->rol === 'SUPER_ADMIN')
                ? 'layouts.admin'
                : 'layouts.gerente';
            $view->with('layout', $layout);
        });
    }
}
