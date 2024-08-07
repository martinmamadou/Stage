<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'required' => false,
                'attr' => ['placeholder' => 'Prénom'],
            ])
            ->add('lastName', TextType::class, [
                'required' => false,
                'attr' => ['placeholder' => 'Nom'],
            ])
            ->add('email', EmailType::class, [
                'required' => false,
                'attr' => ['placeholder' => 'Email'],
            ])

            ->add('color', ColorType::class, [
                'label' => 'couleur',
                'required' => false,
                'attr' => [
                    'type' => 'color'
                ]
            ])
            
            ->add('password', RepeatedType::class, [
                'required' => false,
                'first_options' => [
                    'label' => 'mot de passe',
                    'attr' => ['placeholder' => 'Mot de passe'],
                ], 
                'second_options' => [
                    'label' => 'confirmation mot de passe',
                    'attr' => ['placeholder' => 'Confirmer mot de passe'],
                ],
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent correspondre.',
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
                        'message' => 'Votre mot de passe doit contenir au moins 8 caractères, une lettre majuscule, une lettre minuscule, un chiffre et un caractère spécial.',
                    ]),
                    new Assert\NotBlank(),
                    new Assert\Length(
                        max: 4096,
                    ),
                ]
                ]);

            if ($options['isAdmin']) {
                $builder->remove('password')
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Administrateur' => 'ROLE_ADMIN',
                    'Utilisateur' => 'ROLE_USER',
                ],
                'multiple' => true,
            ]);

           
        }
        if ($options['firstLogin']){
            $builder
            ->remove('firstName')
            ->remove('lastName')
            ->remove('email');
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'isAdmin' => false,
            'firstLogin' => false,
        ]);
    }
}
