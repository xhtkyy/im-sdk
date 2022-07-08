<?php

namespace KyyIM\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use KyyIM\ImServiceManager;
use KyyIM\Interfaces\ImInterface;

class ImServiceProvider extends ServiceProvider {
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot() {
        // 发布配置文件
        $path = realpath(__DIR__ . '/../../config/kyy_im.php');
        $this->publishes([$path => config_path('kyy_im.php')], 'config');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register() {
        $this->app->singleton(ImInterface::class, function ($app) {
            $driver       = $app['config']['kyy_im']['default'];
            $driver_class = Str::studly($driver);
            return ImServiceManager::{$driver_class}($app["config"]["kyy_im"]["drivers"]["$driver"] ?? []);
        });
    }
}
