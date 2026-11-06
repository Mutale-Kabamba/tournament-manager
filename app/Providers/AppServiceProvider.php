<?php

namespace App\Providers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
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
        // Auto-initialize SQLite file and run pending migrations if needed on cloud environments
        try {
            if (config('database.default') === 'sqlite') {
                $dbPath = config('database.connections.sqlite.database');
                if ($dbPath && !file_exists($dbPath) && $dbPath !== ':memory:') {
                    @mkdir(dirname($dbPath), 0755, true);
                    @touch($dbPath);
                }
            }

            if (!Schema::hasTable('tournaments')) {
                Artisan::call('migrate', ['--force' => true]);
            }
        } catch (\Throwable $e) {
            Log::warning('AppServiceProvider boot auto-migration notice: ' . $e->getMessage());
        }
    }
}
