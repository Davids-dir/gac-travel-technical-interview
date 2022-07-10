<?php

namespace App\Form\User;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'Usuario',
                'attr' => [
                    'class' => 'floating-input form-control',
                    'placeholder' => ''
                ],
                /*'row_attr' => [
                    'class' => 'form-floating',
                ],*/
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Contraseña',
                'attr' => [
                    'class' => 'floating-input form-control',
                    'placeholder' => ''
                ],
                /*'row_attr' => [
                    'class' => 'form-floating',
                ],*/
            ])
            ->add('active', HiddenType::class, [
                'required' => false
            ])
            ->add('created_at', HiddenType::class, [
                'required' => false
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Crear cuenta',
                'attr' => [
                    'class' => 'btn btn-primary',
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
