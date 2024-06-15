<?php

namespace App\Form;

use App\Entity\Category;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',null, [
                'attr' => ['class' => 'form-control'],
            ])
            ->add('status', null, [
                'attr' => ['class' => 'is-checkbox'],
            ])
            ->add('featured',null, [
                'attr' => ['class' => 'is-checkbox'],
            ])
            ->add('categoryImage', FileType::class, [
                'label' => 'Category (Image file)',
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
            'data_class' => Category::class,
        ]);
    }
}
