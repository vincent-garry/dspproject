<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => ['placeholder' => 'Email', 'class' => 'form-control']
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les deux mots de passe doivent correspondre.',
                'required' => true,
                'first_options'  => [
                    'label' => 'Mot de passe',
                    'attr' => ['class' => 'form-control', 'placeholder' => 'Mot de passe']
                ],
                'second_options' => [
                    'label' => 'Confirmation de mot de passe',
                    'attr' => ['class' => 'form-control', 'placeholder' => 'Confirmation de mot de passe']
                ],
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Nom',
                'attr' => ['placeholder' => 'Nom', 'class' => 'form-control']
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Prénom',
                'attr' => ['placeholder' => 'Prénom', 'class' => 'form-control']
            ])
            ->add('gender', ChoiceType::class, [
                'label' => 'Genre',
                'attr' => ['class' => 'form-control'],
                'placeholder' => 'Choisissez votre genre',
                'choices'  => [
                    'Homme' => 'male',
                    'Femme' => 'female',
                    'Autre' => 'other',
                    'Préfère ne pas répondre' => 'prefer_not_to_say'
                ],
                'required' => true,
            ])
            ->add('birthdate', TextType::class, [
                'label' => 'Date de naissance',
                'required' => false,
                'attr' => [
                    'class' => 'datepicker',
                    'placeholder' => 'JJ/MM/AAAA'
                ]
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse postale',
                'attr' => ['placeholder' => '12 rue de l\'exemple, 75000 Paris', 'class' => 'form-control']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
