<?php

namespace App\Form;

use App\Entity\Game;
use App\Form\JoueurType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class GameEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('scenario')
            ->add('joueurs', CollectionType::class,
            [
                'entry_type' => JoueurType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'label' => "Liste des joueurs :"
            ])
            ->add('duree', IntegerType::class, array(
                "mapped" => false,
                "data" => 90,
                'label' => "Durée de la bombe (en minutes) :"
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Game::class,
        ]);
    }
}
