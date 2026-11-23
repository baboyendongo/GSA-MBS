<?php

namespace App\Providers;

use App\Models\instance as InstanceModel;
use App\Models\instanceLS;
use App\Models\projectB2B;
use App\Models\Responsable;
use Illuminate\Support\Facades\View;
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
        View::composer([
            'instancels.index',
            'instancels.edit',
            'instances.index',
            'instances.edit',
            'projects-b2b.index',
            'projects-b2b.edit',
        ], function ($view): void {
            $configured = Responsable::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->pluck('name')
                ->all();
            $view->with('responsables', $configured);
        });
    }
}
