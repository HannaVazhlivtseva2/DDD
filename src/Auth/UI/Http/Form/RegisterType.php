<?php

declare(strict_types=1);

namespace App\Auth\UI\Http\Form;

use App\Auth\Domain\Model\Gender;
use App\Auth\UI\Http\Form\Model\RegisterFormModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, ['label' => 'First name'])
            ->add('lastName', TextType::class, ['label' => 'Last name'])
            ->add('email', EmailType::class, ['label' => 'Email'])
            ->add('phone', TelType::class, ['label' => 'Phone'])
            ->add('gender', EnumType::class, [
                'label' => 'Gender',
                'class' => Gender::class,
                'choice_label' => static fn (Gender $gender): string => ucfirst($gender->value),
                'placeholder' => 'Select gender',
            ])
            ->add('plainPassword', PasswordType::class, ['label' => 'Password'])
            ->add('plainPasswordRepeat', PasswordType::class, ['label' => 'Repeat password']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RegisterFormModel::class,
        ]);
    }
}
