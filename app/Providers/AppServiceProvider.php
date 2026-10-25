<?php

namespace App\Providers;

use App\Models\Operator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View as ViewInstance;

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
        View::composer('layout', function (ViewInstance $view): void {
            $view->with('rosterTotal', Operator::query()->count());
        });
    }
}
