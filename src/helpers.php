<?php

declare(strict_types=1);

use DavidIanBonner\Enumerated\Enumerated;
use Illuminate\Database\Eloquent\Model;
use Ollico\AuditLog\LogAuditableEvent;

if (! function_exists('audit_user')) {
    function audit_user(
        Model $dimension,
        Enumerated|string $activity,
        array $props = []
    ): void {
        audit($dimension, $activity, auth()->user(), $props);
    }
}

if (! function_exists('audit')) {
    function audit(
        Model $dimension,
        Enumerated|string $activity,
        ?Model $causer = null,
        array $props = []
    ): void {
        LogAuditableEvent::dispatch(...func_get_args());
    }
}
