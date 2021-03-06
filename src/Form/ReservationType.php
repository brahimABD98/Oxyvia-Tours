<?php

namespace App\Form;

use App\Entity\Reservation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;



class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('date_debut', DateType::class, [
                'widget'=>'single_text',
                'attr' => ['class' => 'js-datepicker'],



            ])

            ->add('date_fin', DateType::class, [
                'widget'=>'single_text',
                'attr' => ['class' => 'js-datepicker'],

            ])

            ->add('nb_personne')

            ->getForm();


        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
