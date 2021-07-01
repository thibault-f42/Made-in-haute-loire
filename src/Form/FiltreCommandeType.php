<?php

namespace App\Form;

use App\Data\SearchCommande;
use App\Data\SearchData;
use App\Entity\Canton;
use App\Entity\Categorie;
use App\Entity\Departement;
use App\Entity\Entreprise;
use App\Entity\EtatCommande;
use App\Entity\Produit;
use App\Entity\SousCategorie;
use App\Repository\CategorieRepository;
use App\Repository\SousCategorieRepository;
use Doctrine\ORM\EntityRepository;
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

class FiltreCommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $entreprise = $options['data']['entreprise']->getId();

        $builder

            ->add('etatCommande', EntityType::class, ['class'=>EtatCommande::class, 'choice_label'=>'etat'])
            ->add('dateMin', DateType::class, ['widget'=>'single_text', 'required'=>false])
            ->add('dateMax', DateType::class, ['widget'=>'single_text','required'=>false])
            ->add('produit' ,EntityType::class, [
                'class' => Produit::class,
                'choice_label' => 'nomArticle',
                'query_builder' => function(EntityRepository $entityRepository) use ($entreprise) {
                    return $entityRepository->
                    createQueryBuilder('p')
                        ->andWhere('p.entreprise = :ent')
                        ->setParameter('ent', $entreprise);
                }])
        ;
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }

}
