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
            ->add('type',ChoiceType::class,array('choices'=>array('Chambre Single'=>'single room','Chambre Double'=>'double room'),'expanded'=>true))
            ->add('prix')
            ->add('image',FileType::class,[
                'mapped' => false])

            ->add('occupe',ChoiceType::class,array('choices'=>array('Occupé'=>'occupe','Non Occupé'=>'non occupe'),'expanded'=>true))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Chambre::class,
        ]);
    }
}
