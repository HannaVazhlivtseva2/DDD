<?php

declare(strict_types=1);

namespace App\Auth\UI\Http\Form\Model;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

final class AvatarUploadFormModel
{
    #[Assert\NotNull(message: 'Choose an image to upload.')]
    #[Assert\Image(
        maxSize: '2M',
        mimeTypes: ['image/jpeg', 'image/png', 'image/webp'],
        mimeTypesMessage: 'Only JPEG, PNG, or WebP images are allowed.',
    )]
    public ?UploadedFile $avatarFile = null;
}
