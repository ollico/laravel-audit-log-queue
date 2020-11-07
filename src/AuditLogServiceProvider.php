<?php

declare(strict_types=1);

namespace Ollico\AuditLog;

use Illuminate\Support\ServiceProvider;

class AuditLogServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        include_once __DIR__ . '/helpers.php';

        $this->publishes([
            __DIR__ . '/config/audit-queue.php' => config_path('audit-queue.php'),
        ]);
    }
}
