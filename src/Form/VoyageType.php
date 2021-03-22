<?php

namespace App\Form;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use App\Entity\Hotel;
use App\Entity\Voyage;
use App\Repository\HotelRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VoyageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('ville')
            ->add('description')

            ->add('date_debut', DateType::class, [
                'widget'=>'single_text',
                'attr' => ['class' => 'js-datepicker'],
                'data'          => new \DateTime(),


            ])

            ->add('date_fin', DateType::class, [
                'widget'=>'single_text',
                'attr' => ['class' => 'js-datepicker'],
                'data'          => new \DateTime(),
            ])
            ->add('nb_personne')
            ->add('prixPersonne')
            ->add('image', FileType::class,[
                'mapped'=>false,
                'label'=>'Affiche de voyage'
            ])

      ->  add('hotel', EntityType::class, [
            'class' => Hotel::class,
          'placeholder' => 'Sélectionner un hotel',
          'query_builder' => function (HotelRepository $er) {
                return $er->createQueryBuilder('u')
                    ->groupBy('u.nom');
            },
            'choice_label' => 'nom',
        ]);


        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Voyage::class,
        ]);
    }
}
