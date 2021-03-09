<?php

namespace App\Form;

use App\Entity\Reservation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use function Sodium\add;


class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

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




            ->add('nb_adulte')
            ->add('nb_enfants')


            ->getForm();



    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}