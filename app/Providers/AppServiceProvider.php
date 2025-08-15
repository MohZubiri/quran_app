<?php

namespace App\Providers;

use App\Models\Branch;
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
        // Share default branch with all views
        View::composer('*', function ($view) {
            $branches = Branch::all();
            $defaultBranch = null;
            
            // If there's only one branch, set it as default
            if ($branches->count() === 1) {
                $defaultBranch = $branches->first();
            }
            
            $view->with('defaultBranch', $defaultBranch);
        });
    }
}
