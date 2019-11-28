<?php

namespace App\Form;

use App\Entity\Region;
use App\Entity\Room;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class RoomType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("summary", TextareaType::class, ['label' => 'Nom à afficher'])
            ->add("address", TextareaType::class, ['label' => 'Adresse complète'])
            ->add("description", TextareaType::class, ['label' => 'Description'])
            ->add("superficy", IntegerType::class, ['label' => 'Superficie'])
            ->add("capacity", IntegerType::class, ['label' => 'Capacité (Nombre de lits)'])
            ->add("price", IntegerType::class, ['label' => 'Prix par nuit (en centimes)'])
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
            ->add('imageName', TextType::class,  ['disabled' => true])
            ->add('imageFile', VichImageType::class, ['required' => false])
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
