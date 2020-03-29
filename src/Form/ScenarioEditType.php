<?php

namespace App\Form;

use App\Form\EtapeType;
use App\Entity\Scenario;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ScenarioEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre')
            ->add('etapes', CollectionType::class,
            [
                'entry_type' => EtapeType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'label' => "Liste des étapes :"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Scenario::class,
        ]);
    }
}
