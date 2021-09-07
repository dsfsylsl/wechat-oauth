<?php

namespace Gxyshs\WechatOauth;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class WechatOauthServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'gxyshs');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'gxyshs');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');
        $this->registerRoutes();

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/wechatoauth.php', 'wechatoauth');

        // Register the service the package provides.
        $this->app->singleton('wechatoauth', function ($app) {
            return new WechatOauth;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['wechatoauth'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/wechatoauth.php' => config_path('wechatoauth.php'),
        ], 'wechatoauth.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/gxyshs'),
        ], 'tigerauth.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/gxyshs'),
        ], 'tigerauth.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/gxyshs'),
        ], 'tigerauth.views');*/

        // Registering package commands.
        // $this->commands([]);
    }

    /**
     * Register the package routes.
     *
     * @return void
     */
    private function registerRoutes()
    {
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__.'/Http/routes.php');
        });
    }

    /**
     * Get the Telescope route group configuration array.
     *
     * @return array
     */
    private function routeConfiguration()
    {
        return [
            // 'domain' => config('telescope.domain', null),
            'namespace' => 'Gxyshs\WechatOauth\Http\Controllers',
            // 'prefix' => config('telescope.path'),
            // 'middleware' => 'telescope',
        ];
    }
}
