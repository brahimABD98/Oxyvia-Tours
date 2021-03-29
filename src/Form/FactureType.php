<?php

namespace App\Form;

use App\Entity\Facture;
use phpDocumentor\Reflection\Types\True_;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FactureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('identifiant',TextType::class,
                ['label'=>'identifiant / passport',
                    'attr'=>['placeholder'=>'merci de tapez votre identifient']
                ])

            ->add('pays')

            ->add('montant',TextType::class,
                ['label'=> 'Montant de la facture'])

            ->add('date_paiement',DateType::class,
                ['label'=>'Date de paiement'])
            ->add('devise', ChoiceType::class, [
                'choices' => [ ''=>'',
                    'Dinar Tunisien TND' => 'Dinar Tunisien TND',
                    'Dinar Algérien DA' => 'Dinar Algérien DA',
                    'Dirham Marocain MAD' => 'Dirham Marocain MAD',
                    'Dollar Américain $' => 'Dollar Américain $',
                    'Dollar Canadien  $ CA '  => 'Dollar Canadien  $ CA ' ,
                    'Euro £' => 'Euro £',
                ],


            ])
            ->add('moyen_paiement',ChoiceType::class, [
                'choices' => [ ''=>'',
                    'Paiement hors ligne' => [
                        'Par espéce' => 'Par espéce',
                        'Par chéque' => 'Par chéque',
                    ],
                    'Paiement On ligne' => [
                        'Paysafecard option' => 'Paysafecard option',
                        'Paypal ' => 'Paypal',
                        'Par carte bancaire ' => 'par carte bancaire',
                    ],
                ],
            ])

            ->add('mode_paiement',ChoiceType::class,[
                'label' => 'Choice',
                'choices' => [

                    'par facilité'=>'par facilité',
                    'comptant' => 'comptant',

                ],
                'expanded' => true,
                'multiple' => false
            ])
            ->add('typeCB',ChoiceType::class,[
                'label' => 'Choice',
                'choices' => [
                    'CB'=>'CB',
                    'Visa' => 'Visa',
                    'Amex' => 'Amex',
                    "Master card" => 'Master card'
                ],
                'expanded' => true,
                'multiple' => false
            ])

            ->add('Ncb',PasswordType::class)
            ->add('code_securite',PasswordType::class)
            ->add('date_expiration')
            ->add('location', ChoiceType::class,
                ['choices'=>[''=>null,'Lac1 (Les berges du Lac)'=>'Lac1 (Les berges du Lac)','Sousse (proche du Mall of Sousse)'=>'Sousse (proche du Mall of Sousse)','Bizete Centre'=>'Bizete Centre'],
                ])

           /* ->add('enabled', ChoiceType::class,
                ['choices'=>[''=>null,'Oui'=>0,'Non'=>1 ],
                ])*/
            ->add('enabled',ChoiceType::class,[
                'label' => 'Choice',
                'choices' => [

                    'Oui'=>0,'Non'=>1

                ],
                'expanded' => true,
                'multiple' => false
            ])
            ->add('color',ColorType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Facture::class,
        ]);
    }
}
