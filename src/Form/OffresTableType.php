<?php

namespace App\Form;

use App\Entity\OffresTable;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class OffresTableType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('IdOffres')
            ->add('nom')
            ->add('date_debut', DateType::class)
            ->add('date_fin' , DateType::class)
            ->add('sujet')
            ->add('prix')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => OffresTable::class,
        ]);
    }
}
