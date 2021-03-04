<?php

namespace App\Form;

use App\Entity\ComptePersonnel;
use App\Entity\Depense;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DepenseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id_personnel',EntityType::class ,[
                'class'=>ComptePersonnel::class,
                'choice_label'=>'id_personnel'
            ])
            ->add('picture',FileType::class, array('data_class' => null))
            ->add('occupation')
            ->add('salaire')
            ->add('horaire_reguliere',ChoiceType::class,[
                'label' => 'Choice',
                'choices' => [

                    'Exelente'=>'Exelente ',
                    'Acceptable' => 'Acceptable',
                    'Insuffisante'=>'Insuffisante',

                ],
                'expanded' => true,
                'multiple' => false
            ])
            ->add('horaire_sup',ChoiceType::class, [
                'choices' => [ ''=>'',
                    'Bonne' => [
                        '90%' => '90%',
                        '80%' => '80%',
                        '70%' => '70%',

                    ],
                    'Moyenne' => [
                        '60%' => '60%',
                        '50%' => '50%',
                        '40%' => '40%',
                    ],
                    'Faible' => [
                        '30%' => '30%',
                        '20%' => '20%',
                        '10%' => '10%',
                    ],
                ],
            ])
            ->add('exempte',ChoiceType::class, [
                'choices' => [ ''=>'',
                    'Oui' => [
                        'Présent & Ponctuel ' => 'Présent & Ponctuel',

                    ],
                    'Non' => [
                        'Absenté (Absence personnel)' => 'Absenté (Absence personnel)',
                        'Absenté (Absence personnelisé) ' => 'Absenté (Absence personnelisé)',
                        'Absenté (Suite au maladie) ' => 'Absenté (Suite au maladie)',
                    ],
                ],
            ])
            ->add('date_depense')

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Depense::class,
        ]);
    }
}
