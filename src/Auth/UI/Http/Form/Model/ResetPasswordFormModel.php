<?php

declare(strict_types=1);

namespace App\Auth\UI\Http\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

final class ResetPasswordFormModel
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 8)]
    public ?string $plainPassword = null;

    #[Assert\NotBlank]
    #[Assert\EqualTo(propertyPath: 'plainPassword', message: 'Passwords do not match.')]
    public ?string $plainPasswordRepeat = null;
}
