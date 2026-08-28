<?php

declare(strict_types=1);

namespace App\Domain\Crm\Enums;

enum ActivityType: string
{
    case Call = 'call';
    case Email = 'email';
    case Meeting = 'meeting';
    case Task = 'task';
    case Other = 'other';
}
