<?php

namespace App\Form;

use App\Entity\Utilisateur;
use App\Entity\Ville;
use App\Repository\VilleRepository;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use function Cassandra\Type;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('prenom')
            ->add('email')
            ->add('adresse')
            ->add('codePostal', TextType::class, [
                'mapped'=>false,
                'attr' => ['placeholder'=>'Indiquez votre code postal'],
                'label'=> 'Code Postal'
                ])
            ->add('telephone')
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'En vous inscrivant vous acceptez nos conditions d utilisation.',
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                // Ajouts pour le type Repeated
                // Doivent être placés au début
                'type' => PasswordType::class,
                'invalid_message' => 'Les valeurs pour les champs mots de passe doivent être identiques.',
                'options' => ['attr' => ['class' => 'password-field']],
                'first_options'  => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Répétez le mot de passe'],

                // Code généré par la commande 'make:registration-form'
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Mot de passe obligatoire',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Le mot de passe doit avoir une longueur minimum de {{ limit }} caractères.',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
        ;

         $builder->get('codePostal')->addEventListener(FormEvents::POST_SUBMIT,
         function ( FormEvent $saisieCodePostal){
             $form = $saisieCodePostal->getForm();
                     $this->addVilleField($form->getParent(), $form->getData());
             ;
         });

         $builder->addEventListener(
             FormEvents::POST_SET_DATA,
             function (FormEvent $event) {
                 $data = $event->getData();
                 $ville = $data->getVille();
                 $form = $event->getForm();
                 if ($ville) {
                     $codePostal = $ville->getCodePostal();
                     $this->addVilleField($form, $codePostal);
                     $form->get('codePostal')->setData($codePostal);
                 } else {
                     $this->addVilleField($form, '');
                 }
             }
         );
    }

     private function addVilleField(FormInterface $form, ?string $codePostal)
     {
         if ($codePostal) {

             $form->add('ville', EntityType::class, [
                 'class' => Ville::class,
                 'label' => 'Ville',
                 'required' => false,
                 'expanded' => false,
                 'multiple' => false,
                 'placeholder' => 'Ville',
                 'choice_label' => 'nom',
                 'query_builder' => function(VilleRepository $villeRepository) use ($codePostal) {
                     return $villeRepository->getVillesByCodePostal($codePostal);
                 },
                 'constraints' => [
                     new NotBlank([
                         'message' => 'Veuillez choisir votre ville' ,
                     ]),
                 ],
             ]);
         }
         else {
             $form->add('ville', EntityType::class, [
                 'class' => Ville::class,
                 'label' => 'Ville',
                 'required' => false,
                 'expanded' => false,
                 'multiple' => false,
                 'placeholder' => 'Ville',
                 'choice_label' => 'nom',
                 'choices' => [],
                 'constraints' => [
                     new NotBlank([
                         'message' => 'Veuillez choisir votre ville',
                     ]),
                 ],
             ]);
         }
     }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
