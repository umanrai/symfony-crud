<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',null, [
                'attr' => ['class' => 'form-control'],
            ])
            ->add('email', null, [
                'attr' => ['class' => 'form-control'],
            ])
            ->add('password',PasswordType::class,[
                'attr' => [
                    'class' => 'form-control',
                    'required' => false
                ],
            ])
            ->add('userImage', FileType::class, [
                'label' => 'User (Image file)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '3000k',
                        'mimeTypes' => [
                            'image/*',
//                            'application/pdf',
//                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => 'Please upload an image',
                    ])
                ],
                'attr' => [
                    'class' => 'form-control image-input',
                ],
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
