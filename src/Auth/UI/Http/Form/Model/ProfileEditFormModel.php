<?php

declare(strict_types=1);

namespace App\Auth\UI\Http\Form\Model;

use App\Auth\Domain\Model\Gender;
use App\Auth\Domain\Model\User;
use Symfony\Component\Validator\Constraints as Assert;

final class ProfileEditFormModel
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    public ?string $firstName = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    public ?string $lastName = null;

    #[Assert\NotBlank]
    #[Assert\Regex(pattern: '/^\+?[0-9\s\-()]{7,20}$/', message: 'Enter a valid phone number.')]
    public ?string $phone = null;

    #[Assert\NotNull]
    public ?Gender $gender = null;

    public static function fromUser(User $user): self
    {
        $formModel = new self();
        $formModel->firstName = $user->firstName();
        $formModel->lastName = $user->lastName();
        $formModel->phone = $user->phone()->toString();
        $formModel->gender = $user->gender();

        return $formModel;
    }
}
