<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Produit;
use App\Entity\SousCategorie;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
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
            ->add('sousCategorie',EntityType::class, [
                'class' => SousCategorie::class,
                'choice_label' => 'nom',
                'query_builder' => function(EntityRepository $entityRepository)
                {
                    return $entityRepository->createQueryBuilder('c')->orderBy('c.nom');
                }])
            ->add('photos', FileType::class, ['label' => 'Photos', 'multiple' => true, 'mapped' => false, 'required' => false])

        ;
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
