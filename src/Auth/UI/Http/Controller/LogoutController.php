<?php

declare(strict_types=1);

namespace App\Auth\UI\Http\Controller;

use Symfony\Component\Routing\Attribute\Route;

final class LogoutController
{
    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function __invoke(): never
    {
        throw new \LogicException('This route is intercepted by the firewall logout listener and should never be reached.');
    }
}
