<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\Operater;
use App\Models\ZahtevPredaje;

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
        app()->setLocale('sr');

        Gate::define('super-admin', function ($user) {
            return $user->is_super_admin === true;
        });

        View::composer('components.layouts.admin', function ($view) {
            $view->with([
                'dozvoleAlertCount' => Operater::where('aktivan', true)
                    ->whereNotNull('dozvola_vazi_do')
                    ->where(function ($q) {
                        $q->where('dozvola_vazi_do', '<', now())
                            ->orWhereBetween('dozvola_vazi_do', [now(), now()->addDays(30)]);
                    })
                    ->count(),
                'naCekanjuCount' => ZahtevPredaje::naCekanju()->count(),
            ]);
        });
    }
}
