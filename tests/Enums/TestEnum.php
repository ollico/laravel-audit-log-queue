<?php

namespace Ollico\AuditLog\Tests\Enums;

use DavidIanBonner\Enumerated\Enumerated;
use DavidIanBonner\Enumerated\HasEnumeration;

enum TestEnum: string implements Enumerated
{
    use HasEnumeration;

    case ENUM = 'enum';

    public static function key(): string
    {
        return 'enum';
    }
}
