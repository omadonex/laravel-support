<?php

namespace Omadonex\LaravelSupport\Providers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Omadonex\LaravelSupport\Commands\Database\UnsafeSeeding;
use Omadonex\LaravelSupport\Commands\Module\Make;
use Omadonex\LaravelSupport\Commands\Module\MakeModel;
use Omadonex\LaravelSupport\Commands\Module\RemoveModel;

class SupportServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $pathRoot = realpath(__DIR__.'/../..');

        $this->loadMigrationsFrom("{$pathRoot}/database/migrations");
        $this->loadViewsFrom("{$pathRoot}/resources/views", 'support');
        $this->loadTranslationsFrom("{$pathRoot}/resources/lang", 'support');

        $this->publishes([
            "{$pathRoot}/config/locale.php" => config_path('omx/locale.php'),
            "{$pathRoot}/config/modules.php" => config_path('modules.php'),
        ], 'config');
        $this->publishes([
            "{$pathRoot}/resources/views" => resource_path('views/vendor/support'),
        ], 'views');
        $this->publishes([
            "{$pathRoot}/resources/lang" => resource_path('lang/vendor/support'),
        ], 'translations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                Make::class,
                MakeModel::class,
                RemoveModel::class,
            ]);
        }

        $this->commands([
            UnsafeSeeding::class,
        ]);

        Validator::extend('time', 'Omadonex\LaravelSupport\Services\CustomValidator@timeValidate');
        Validator::extend('phone', 'Omadonex\LaravelSupport\Services\CustomValidator@phoneValidate');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }
}
