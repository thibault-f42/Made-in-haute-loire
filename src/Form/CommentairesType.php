<?php

namespace App\Form;

use App\Entity\Commentaires;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentairesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
//            ->add('email', EmailType::class, [
//                'label' => 'Votre adresse mail :', 'mapped' => false
//            ])
            ->add('pseudo' , TextType::class, [
                'label' => 'Votre pseudonyme :',
                'attr' => ['class'=>'pseudo_input']
            ])
            ->add('content', TextareaType::class,  [
                'label' => 'Votre commentaire :',
                'attr' => ['class'=>'commentaire_input']
            ])

            ->add('rgpd', CheckboxType::class, [
               'label' => "J'ai lu et j'accepte le Règlement Général sur la Protection des Données"
            ])

            ->add('parentid', HiddenType::class, [
                'mapped' => false
            ])
            ->add('envoyer', SubmitType::class, [
                'attr' => ['class'=>'commentaire_envoyer']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commentaires::class,
        ]);
    }

}
