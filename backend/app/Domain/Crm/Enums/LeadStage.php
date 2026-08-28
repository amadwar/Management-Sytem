<?php

declare(strict_types=1);

namespace App\Domain\Crm\Enums;

enum LeadStage: string
{
    case New = 'new';
    case Contacted = 'contacted';
    case Qualified = 'qualified';
    case Proposal = 'proposal';
    case Won = 'won';
    case Lost = 'lost';
}
