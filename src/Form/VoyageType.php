<?php

namespace App\Form;

use App\Entity\Voyage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
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



            ])

            ->add('date_fin', DateType::class, [
                'widget'=>'single_text',
                'attr' => ['class' => 'js-datepicker'],

            ])
            ->add('nb_personne')
            ->add('prixPersonne')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Voyage::class,
        ]);
    }
}
