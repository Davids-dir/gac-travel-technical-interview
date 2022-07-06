<?php

namespace App\Form\Product;

use App\Entity\Category;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditProductStock extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nombre',
                'attr' => [
                    'class' => 'form-control',
                    'readonly' => true
                ]
            ])
            ->add('category', EntityType::class, [
                'label' => 'Categoría',
                'class' => Category::class,
                'choice_label' => 'name',
                'disabled' => true,
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('stock', null, [
                'label' => 'Cantidad a agregar o eliminar del stock',
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => true
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Guardar cambios',
                'attr' => [
                    'class' => 'btn btn-primary m-2'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
