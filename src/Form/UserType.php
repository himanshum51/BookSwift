<?php

namespace App\Form;

use App\Entity\OrganizerProfile;
use App\Entity\User;
use App\Entity\UserProfile;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('roles')
            ->add('password')
            ->add('isVerified')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('verificationToken')
            ->add('resetPasswordToken')
            ->add('resetPasswordRequestedAt')
            ->add('userProfile', EntityType::class, [
                'class' => UserProfile::class,
                'choice_label' => 'id',
            ])
            ->add('organizerProfile', EntityType::class, [
                'class' => OrganizerProfile::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
