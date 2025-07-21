<?php

namespace App\Form;

use App\Entity\OrganizerProfile;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrganizerProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('companyName')
            ->add('bio')
            ->add('website')
            ->add('phoneNumber')
            ->add('address')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('photo')
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OrganizerProfile::class,
        ]);
    }
}
