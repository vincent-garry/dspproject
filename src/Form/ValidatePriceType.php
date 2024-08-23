<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Code;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ValidatePriceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email',
                'placeholder' => 'Sélectionnez un utilisateur',
                'label' => 'Utilisateur',
                'attr' => [
                    'class' => 'js-select2'
                ]
            ])
            ->add('code', TextType::class, [
                'label' => 'Code à valider',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Entrez le code à valider'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Valider le code',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
        ]);
    }
}
