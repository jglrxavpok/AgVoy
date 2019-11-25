<?php

namespace App\Form;

use App\Entity\Region;
use App\Entity\Room;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoomType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("summary", TextareaType::class, ['label' => 'Nom à afficher'])
            ->add("address", TextareaType::class, ['label' => 'Adresse complète'])
            ->add("description", TextareaType::class, ['label' => 'Description'])
            ->add("superficy", TextareaType::class, ['label' => 'Superficie'])
            ->add("capacity", TextareaType::class, ['label' => 'Capacité (Nombre de lits)'])
            ->add("price", TextareaType::class, ['label' => 'Prix par nuit'])
            ->add("region", EntityType::class, [
                'choices' => $options['regions'],
                'class' => Region::class,
                'multiple' => true,
                'expanded' => true,
                'choice_label' => function($region, $key, $index) {
                    return $region->getName();
                },
                'label' => "Régions",
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Room::class,
            'regions' => null,
        ])
            ->setAllowedTypes('regions', array('array'))
            ->setRequired('regions')
        ;
    }
}
