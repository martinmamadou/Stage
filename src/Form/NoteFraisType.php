<?php
namespace App\Form;

use App\Entity\Taxe;
use App\Entity\User;
use App\Entity\Client;
use App\Entity\Forfait;
use App\Entity\NoteFrais;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class NoteFraisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre',
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
            ->add('carte_client', CheckboxType::class, [
                'label' => 'Carte Client',
                'required' => false
            ])
            ->add('creation', DateType::class, [
                'label' => 'Date de Création'
            ])
            ->add('categorie', ChoiceType::class, [
                'choices' => [
                    'Hotel' => 'Hotel',
                    'Frais Kilométrique' => 'Voiture',
                    'Forfait' => 'forfait',
                    'Train' => 'Train',
                    'Billet Avion' => 'Avion'
                ],
                'placeholder' => 'Choisir une catégorie',
                'required' => false,
            ])
            ->add('prixHt', MoneyType::class, [
                'label' => 'Prix HT',
                'required' => false
            ])
            ->add('quantite', NumberType::class, [
                'label' => 'Quantité',
                'required' => false
            ])
            ->add('km', NumberType::class, [
                'label' => 'Km',
                'required' => false
            ])
            ->add('taxe', EntityType::class, [
                'class' => Taxe::class,
                'label' => 'Taxe',
                'placeholder' => 'Sélectionner une taxe',
                'choice_label' => 'name',
                'required' => false
            ])
            ->add('forfait', EntityType::class, [
                'class' => Forfait::class,
                'choice_label' => function (Forfait $forfait) {
                    return $forfait->getName();
                },
                'placeholder' => 'Sélectionner un forfait',
                'required' => false,
                'choice_attr' => function (Forfait $forfait) {
                    return [
                        'data-somme' => $forfait->getSomme()
                    ];
                }
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => NoteFrais::class,
        ]);
    }
}
