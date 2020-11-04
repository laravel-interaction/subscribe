<?php


namespace Zing\LaravelSubscribe;


use Illuminate\Support\ServiceProvider;

class SubscribeServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([$this->getConfigPath() => config_path('subscribe.php')], 'config');
            $this->loadMigrationsFrom(__DIR__ . '/../migrations');
        }
    }

    public function register()
    {
        $this->mergeConfigFrom($this->getConfigPath(), 'subscribe');
    }

    protected function getConfigPath(): string
    {
        return __DIR__ . '/config/subscribe.php';
    }
}