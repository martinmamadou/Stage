<?php

namespace App\Form;

use App\Entity\Taxe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;

class TaxeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name', TextType::class, [
            'label' => 'Nom de taxe :',
            'required' => false,
            'attr' => [
                'placeholder' => 'TVA X'
            ]
        ])
        ->add('rate', PercentType::class, [
            'label' => 'Taux :',
            'required' => false,
            'invalid_message' => 'Remplir un taux valide',
            'scale' => 2,
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Taxe::class,
        ]);
    }
}
