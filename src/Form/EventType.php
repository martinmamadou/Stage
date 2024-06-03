<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre' ,TextType::class,[
               'label' => 'Titre',
               'required' => false,
               'attr' => [
               'placeholder' => 'mon super titre'
               ]
            ])
            ->add('start_date', DateTimeType::class,[
                'label' => 'Date de Depart',
                'required' => false 
            ])
            ->add('end_date', DateTimeType::class,[
                'label' => 'Date de Retour',
                'required' => false 
            ])
            ->add('color', ColorType::class, [
                'label' => 'couleur',
                'required' => false,
                'attr' => [
                    'type' => 'color'
                ]
            ])
            ->add('description', TextType::class,[
                'label' => 'description',
                'required' => false,
                'attr' => [
                    'placeholder' => 'ma super description'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
