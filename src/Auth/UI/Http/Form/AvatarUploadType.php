<?php

declare(strict_types=1);

namespace App\Auth\UI\Http\Form;

use App\Auth\UI\Http\Form\Model\AvatarUploadFormModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AvatarUploadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('avatarFile', FileType::class, [
            'label' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AvatarUploadFormModel::class,
        ]);
    }
}
