<?php

declare(strict_types=1);

namespace App\Domain\Crm\Enums;

enum CustomerStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Prospect = 'prospect';
    case Blocked = 'blocked';
}
