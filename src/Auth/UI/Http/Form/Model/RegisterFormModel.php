<?php

declare(strict_types=1);

namespace App\Auth\UI\Http\Form\Model;

use App\Auth\Domain\Model\Gender;
use Symfony\Component\Validator\Constraints as Assert;

final class RegisterFormModel
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    public ?string $firstName = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    public ?string $lastName = null;

    #[Assert\NotBlank]
    #[Assert\Email]
    public ?string $email = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 8)]
    #[Assert\Regex(
        pattern: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z0-9]).+$/',
        message: 'Password must include an uppercase letter, a lowercase letter, a digit, and a symbol.',
    )]
    public ?string $plainPassword = null;

    #[Assert\NotBlank]
    #[Assert\EqualTo(propertyPath: 'plainPassword', message: 'Passwords do not match.')]
    public ?string $plainPasswordRepeat = null;

    #[Assert\NotBlank]
    #[Assert\Regex(pattern: '/^\+?[0-9\s\-()]{7,20}$/', message: 'Enter a valid phone number.')]
    public ?string $phone = null;

    #[Assert\NotNull]
    public ?Gender $gender = null;
}
