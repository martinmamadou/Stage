<?php

namespace App\Form;

use App\Entity\Taxe;
use App\Entity\User;
use App\Entity\Devis;
use App\Entity\Client;
use Doctrine\DBAL\Types\FloatType;
use Doctrine\DBAL\Types\DecimalType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat\Wizard\Number;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class DevisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'titre',
                'required' => false
            ])
            ->add('client', EntityType::class, [
                'class' => Client::class,
                'choice_label' => 'name',
            ])
            ->add('employe', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'FirstName',
            ])
            ->add('carte_client', CheckboxType::class,[
                'label' => 'carte client',
                'required' => false
            ])
            ->add('creation',DateType::class,[
                'label'=>'creation'
            ])
            ->add('categorie', ChoiceType::class, [
                'choices' => [
                    'Hotel' => 'Hotel',
                    'Voiture' => 'Voiture',
                ],
                'placeholder' => 'Choose a category',])
            ->add('prixHt',MoneyType::class,[
                'label'=> 'prixHt',
                'required'=> false
            ])
            ->add('quantite',NumberType::class,[
                'label'=> 'Quantité',
                'required'=> false])
            ->add('km',NumberType::class,[
                'label'=> 'Km',
                'required'=> false])
            ->add('prixKm',NumberType::class,[
                'label'=> 'Km',
                'required'=> false])
                ->add('taxe', EntityType::class, [
                    'class' => Taxe::class,
                    'label' => 'Taxe',
                    'placeholder' => 'Sélectionner une taxe',
                    'choice_label' => 'name',

                    'multiple' => false,
                    'expanded' => false,
                ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Devis::class,
        ]);
    }
}
