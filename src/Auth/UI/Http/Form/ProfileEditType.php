<?php

declare(strict_types=1);

namespace App\Auth\UI\Http\Form;

use App\Auth\Domain\Model\Gender;
use App\Auth\UI\Http\Form\Model\ProfileEditFormModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ProfileEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, ['label' => 'First name'])
            ->add('lastName', TextType::class, ['label' => 'Last name'])
            ->add('phone', TelType::class, ['label' => 'Phone'])
            ->add('gender', EnumType::class, [
                'label' => 'Gender',
                'class' => Gender::class,
                'choice_label' => static fn (Gender $gender): string => ucfirst($gender->value),
                'placeholder' => 'Select gender',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProfileEditFormModel::class,
        ]);
    }
}
