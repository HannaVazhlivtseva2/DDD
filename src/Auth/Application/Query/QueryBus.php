<?php

declare(strict_types=1);

namespace App\Auth\Application\Query;

interface QueryBus
{
    public function ask(object $query): mixed;
}
