<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class JoueurType extends AbstractType
{

    /**
     * Création d'un formulaire pour le participant
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('email', EmailType::class, [
            'required' => true, // bien que required==true soit déjà par défaut
            'constraints' =>         
            new Length([
                'min' => 8,
                'max' => 60,
                'minMessage' => 'L\'email doit comporter {{ limit }} caractères ou plus',
                'maxMessage' => 'L\'email ne doit pas comporter plus de {{ limit }} caractères',
            ]),
            'attr' => [
                'placeholder' => 'Saisissez ici votre email pour participer !'
            ]
        ])
        ->add('Envoyer', SubmitType::class, [
            'label' => 'Je participe !',
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
