<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Produit;
use App\Entity\SousCategorie;
use App\Repository\SousCategorieRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nomArticle')
            ->add('description')
            ->add('prix')
            ->add('stock')
            ->add('categorie', EntityType::class,['class' => Categorie::class,
                'choice_label' => 'libelle', 'placeholder' => 'Catégorie', 'required'=>false ])

            ->add('photos', FileType::class, ['label' => 'Photos', 'multiple' => true, 'mapped' => false, 'required' => false])
            ->add('activeChat')

        ;
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
                $sousCategorie = $data->getSousCategorie();
                $form = $event->getForm();
                if (!empty($sousCategorie)) {
                    $categorie = $sousCategorie->getCategorie();
                    $this->addSousCategorieField($form, $categorie);
                    $form->get('categorie')->setData($categorie);
                } else {
                    $this->addSousCategorieField($form, '');
                }
            }
        );
    }

    private function addSousCategorieField(FormInterface $form, $categorie)
    {
        if ($categorie) {

            $form->add('sousCategorie', EntityType::class, [
                'class' => SousCategorie::class,
                'label' => 'Sous-catégorie',
                'required' => false,
                'expanded' => false,
                'multiple' => false,
                'placeholder' => 'Sous-Categorie',
                'choice_label' => 'nom',
                'query_builder' => function(SousCategorieRepository $CategorieRepository) use ($categorie) {
                    return  $CategorieRepository->getSousCategorieByCategorieId($categorie->getId())
                        ;
                }
            ]);
        }
        else {
            $form->add('sousCategorie', EntityType::class, [
                'class' => SousCategorie::class,
                'label' => 'Sous-catégorie',
                'required' => false,
                'expanded' => false,
                'multiple' => false,
                'placeholder' => 'Sous-catégories',
                'choice_label' => 'nom',
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
