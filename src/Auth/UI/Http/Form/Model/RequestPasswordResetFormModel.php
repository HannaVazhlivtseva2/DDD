<?php

declare(strict_types=1);

namespace App\Auth\UI\Http\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

final class RequestPasswordResetFormModel
{
    #[Assert\NotBlank]
    #[Assert\Email]
    public ?string $email = null;
}
