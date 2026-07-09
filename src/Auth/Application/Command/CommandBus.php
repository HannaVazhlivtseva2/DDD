<?php

declare(strict_types=1);

namespace App\Auth\Application\Command;

interface CommandBus
{
    public function dispatch(object $command): mixed;
}
