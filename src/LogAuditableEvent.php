<?php

declare(strict_types=1);

namespace Ollico\AuditLog;

use DavidIanBonner\Enumerated\Enumerated;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LogAuditableEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var Model */
    public $dimension;

    /** @var ?Model */
    public $causer = null;

    /** @var string */
    public $activity;

    /** @var array */
    public $props = [];

    public function __construct(
        Model $dimension,
        Enumerated|string $activity,
        ?Model $causer = null,
        array $props = []
    ) {
        $this->queue = config('audit-queue.queue', null);
        $this->dimension = $dimension;
        $this->activity = $activity instanceof Enumerated ? $activity->value : $activity;
        $this->causer = $causer;
        $this->props = $props;
    }

    public function handle(): void
    {
        $enumInstance = config('audit-queue.enum');

        if (!$enumInstance) {
            throw new Exception('No Enum instance could be found.');
        }

        (new AuditLog())
            ->causer($this->causer ?: null)
            ->dimension($this->dimension)
            ->properties($this->props)
            ->log($enumInstance::from($this->activity));
    }
}
