<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;

Artisan::command('platform:about', function (): void {
    $this->info('MDR Business Platform - Phase 1');
});
