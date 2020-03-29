<?php

namespace App\Form;

use App\Entity\Joueur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class JoueurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('prenom')
            ->add('age')
            ->add('sexe')
            ->add('sexe', ChoiceType::class, [
                'choices' => [
                    'Homme' => "m",
                    'Femme' => "f",
                ]])
            ->add('codeBarre', ChoiceType::class, [
                'choices' => [
                    '020251' => "020251",
                    '020252' => "020252",
                    '020253' => "020253",
                    '020254' => "020254",
                    '020255' => "020255",
                    '020256' => "020256",
                ]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Joueur::class,
        ]);
    }
}
