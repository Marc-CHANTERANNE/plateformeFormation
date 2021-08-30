<?php

namespace App\Form;

use App\Entity\Article;
use DateTimeInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class ArticleFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('creationDate', DateType::class, [
                'widget' => 'choice',
                'days' => range(1,31),
                'format' => 'dd-MM-yyyy',
                'years' => range(1920, 2021)
            ])
            ->add('modificationDate', DateType::class, [
                'widget' => 'choice',
                'days' => range(1,31),
                'format' => 'dd-MM-yyyy',
                'years' => range(1920, 2021)
            ])
            ->add('autor')
            ->add('image', FileType::class, [
                'label' => 'Image de l\'article',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png'
                        ],
                        'mimeTypesMessage' => 'Mettez un format d\'image valide (PNG ou JPG)'

                    ])
                ]
            ])
            ->add('content')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
