<?php

declare(strict_types=1);

namespace Ollico\AuditLog;

use DavidIanBonner\Enumerated\Enum;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LogAuditableEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Model $dimension;

    public ?Model $causer = null;

    public string $activity;

    public array $props = [];

    public function __construct(
        Model $dimension,
        string $activity,
        ?Model $causer = null,
        array $props = []
    ) {
        $this->queue = 'auditlog';
        $this->dimension = $dimension;
        $this->activity = $activity;
        $this->causer = $causer;
        $this->props = $props;
    }

    public function handle(): void
    {
        (new AuditLog())
            ->causer($this->causer ?: null)
            ->dimension($this->dimension)
            ->properties($this->props)
            ->log(new Enum($this->activity));
    }
}
