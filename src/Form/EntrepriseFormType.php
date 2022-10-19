<?php

namespace App\Form;

use App\Entity\Entreprise;
use App\Entity\Ville;
use App\Repository\VilleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use function Sodium\add;

class EntrepriseFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('siret', TextType::class, ['required' => true, 'label' => 'SIRET'])
            ->add('nom', TextType::class, ['required' => true])
            ->add('adresse', TextType::class, ['required' => true])
            ->add('telephone', TextType::class, ['required' => true])
            ->add('email', TextType::class, ['required' => true])
            ->add('photos', FileType::class, ['label' => 'Photos', 'multiple' => true, 'mapped' => false, 'required' => false])
            ->add('codePostal', TextType::class, [
                'mapped' => false,
                'attr' => ['placeholder' => 'Indiquez votre code postal'],
                'label' => 'Code Postal',
                'required' => true
            ])
            ->add('justificatifSiret', FileType::class, ['label' => 'Extrai de Kbis', 'multiple' => true, 'mapped' => false, 'required' => false ])
            ->add('carteIdentite', FileType::class, ['label' => 'Carte d\'identité', 'multiple' => true, 'mapped' => false, 'required' => false ])
            ->add('description', TextareaType::class, ['required'=>true, 'label'=>'Description de votre entreprise'])
        ;

        $builder->get('codePostal')->addEventListener(FormEvents::POST_SUBMIT,
            function ( FormEvent $saisieCodePostal){
                $form = $saisieCodePostal->getForm();
                if (strlen($form->getData()) >= 2) {
                    $this->addVilleField($form->getParent(), $form->getData());
                }
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
                    return  $villeRepository->getVillesByCodePostal($codePostal)
                    ;
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
            'data_class' => Entreprise::class,
        ]);
    }
}
