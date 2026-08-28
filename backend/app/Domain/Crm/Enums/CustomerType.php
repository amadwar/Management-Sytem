<?php

declare(strict_types=1);

namespace App\Domain\Crm\Enums;

enum CustomerType: string
{
    case Person = 'person';
    case Company = 'company';
}
