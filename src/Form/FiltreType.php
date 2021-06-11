<?php

namespace App\Form;

use App\Data\SearchData;
use App\Entity\Categorie;
use App\Entity\Departement;
use App\Entity\SousCategorie;
use App\Repository\CategorieRepository;
use App\Repository\SousCategorieRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class FiltreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('categorie', EntityType::class,['class' => Categorie::class,
                'choice_label' => 'libelle'])
            ->add('departement', EntityType::class,['class' => Departement::class,
                'choice_label' => 'nom'])
            ->add('canton')
            ->add('prixMin')
            ->add('prixMax')
        ;


        $builder->get('categorie')->addEventListener(FormEvents::POST_SUBMIT,
            function ( FormEvent $saisieCategorie){
                $form = $saisieCategorie->getForm();
                    $this->addSousCategorieField($form->getParent(), $form->getData());
            });

        $builder->addEventListener(
            FormEvents::POST_SET_DATA,
            function (FormEvent $event) {
                $data = $event->getData();
                $sousCategorie = $data->getSousCategories();
                $form = $event->getForm();
                if (!empty($sousCategorie[0])) {
                    $categorie = $sousCategorie->getCategorie();
                    $this->addSousCategorieField($form, $categorie);
                    $form->get('categorie')->setData($categorie);
                } else {
                    $this->addSousCategorieField($form, '');
                }
            }
        );
    }

    private function addSousCategorieField(FormInterface $form, ?string $categorie)
    {
        if ($categorie) {

            $form->add('sousCategorie', EntityType::class, [
                'class' => SousCategorie::class,
                'label' => 'Sous-Catégorie',
                'required' => false,
                'expanded' => false,
                'multiple' => false,
                'placeholder' => 'Sous-Categorie',
                'choice_label' => 'nom',
                'query_builder' => function(SousCategorieRepository $CategorieRepository) use ($categorie) {
                    return  $CategorieRepository->getSousCategorieByCategorieId($categorie)
                        ;
                },
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez choisir votre sous-categorie' ,
                    ]),
                ],
            ]);
        }
        else {
            $form->add('sousCategorie', EntityType::class, [
                'class' => SousCategorie::class,
                'label' => 'Sous-catégorie',
                'required' => false,
                'expanded' => false,
                'multiple' => false,
                'placeholder' => 'Sous-catégorie',
                'choice_label' => 'nom',
                'choices' => [],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez choisir une catégorie',
                    ]),
                ],
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SearchData::class,
            'METHOD' => 'GET',
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }

}
