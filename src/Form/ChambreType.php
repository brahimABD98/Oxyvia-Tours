<?php

namespace App\Form;

use App\Entity\Chambre;
use App\Entity\Hotel;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ChambreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('idhotel',EntityType::class, [
                'class' => Hotel::class,
                'choice_label' => 'name',
            ])
            ->add('numero')
            ->add('type',ChoiceType::class,array('choices'=>array('Suite'=>'Suite','Chambre Single'=>'Chambre Single','Chambre Double'=>'Chambre Double','Chambre Triple'=>'Chambre Triple'),'expanded'=>true))
            ->add('prix')
            ->add('image',FileType::class,[
                'mapped' => false])

            ->add('occupe',ChoiceType::class,array('choices'=>array('Occupé'=>'Occupé','Non Occupé'=>'Non Occupé'),'expanded'=>true))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Chambre::class,
        ]);
    }
}
