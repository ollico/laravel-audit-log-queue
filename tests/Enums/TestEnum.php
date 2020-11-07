<?php

namespace Ollico\AuditLog\Tests\Enums;

use DavidIanBonner\Enumerated\Enum;

class TestEnum extends Enum
{
    public const ENUM = 'enum';

    public function langKey(): string
    {
        return 'enum';
    }
}
