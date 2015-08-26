<?php

namespace App\Libs\Providers;

use Illuminate\Support\ServiceProvider;

class LibsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerHelpers();
    }

    protected function registerHelpers()
    {
        require __DIR__ . '/../Helpers/helpers.php';
    }
}
