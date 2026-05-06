<?php

namespace App\Providers;

use Dcat\Admin\Grid\Column;
use Illuminate\Support\ServiceProvider;

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
        //
        Column::extend('datetimeSplit', function ($value) {
            if (!$value) {
                return '';
            }
            return "<div>" . substr($value, 0, 10) . "</div><div>" . substr($value, 10) . "</div>";
        });

        require_once app_path('helpers.php');
    }
}
