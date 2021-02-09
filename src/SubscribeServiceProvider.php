<?php

declare(strict_types=1);

namespace LaravelInteraction\Subscribe;

use Illuminate\Support\ServiceProvider;

class SubscribeServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes(
                [
                    $this->getConfigPath() => config_path('subscribe.php'),
                ],
                'subscribe-config'
            );
            $this->publishes(
                [
                    $this->getMigrationsPath() => database_path('migrations'),
                ],
                'subscribe-migrations'
            );
            if ($this->shouldLoadMigrations()) {
                $this->loadMigrationsFrom($this->getMigrationsPath());
            }
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom($this->getConfigPath(), 'subscribe');
    }

    protected function getConfigPath(): string
    {
        return __DIR__ . '/../config/subscribe.php';
    }

    protected function getMigrationsPath(): string
    {
        return __DIR__ . '/../migrations';
    }

    private function shouldLoadMigrations(): bool
    {
        return (bool) config('subscribe.load_migrations');
    }
}
