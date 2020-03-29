<?php

namespace App\Form;

use App\Entity\Etape;
use App\Form\IndiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class EtapeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre')
            ->add('descriptif')
            ->add('automatique')
            ->add('parents')
            ->add('indices', CollectionType::class,
            [
                'entry_type' => IndiceType::class,
                'allow_add' => false,
                'allow_delete' => false,
                'label' => "Liste des indices :",
                'attr' => array ('readonly' => true)
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Etape::class,
        ]);
    }
}
