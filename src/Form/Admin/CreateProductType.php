<?php

namespace App\Form\Admin;

use App\Entity\Attribute;
use App\Entity\AttributeOptions;
use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Store;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;

class CreateProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'required' => true,
                'constraints' => [
                    new Length([
                        'min' => 2,
                        'minMessage' => "The category must have at least {{ limit }} characters"
                    ])
                ]
            ])
            ->add('brand', TextType::class, [
                'required' => true,
                'constraints' => [
                    new Length([
                        'min' => 1,
                        'minMessage' => "The brand must have at least {{ limit }} characters"
                    ])
                ]
            ])
            ->add('ord', IntegerType::class, [
                'required' => true,
                'constraints' => [
                    new Regex([
                        'pattern' => '/^[1-9][0-9]*$/'
                    ])
                ]
            ])
            ->add('description', TextareaType::class, [
                'required' => true,
                'constraints' => [
                    new Length([
                        'min' => 10,
                        'minMessage' => 'Description must be at least {{ limit }} characters long'
                    ])
                ]
            ])
            ->add('price', MoneyType::class, [
                'required' => true,
                'constraints' => [
                    new Regex([
                        'pattern' => '/^[1-9][0-9]*$/'
                    ])
                ]
            ])
            ->add('qty', IntegerType::class, [
                'required' => true,
                'constraints' => [
                    new Regex([
                        'pattern' => '/^[1-9][0-9]*$/'
                    ])
                ]
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
            ])
            ->add('attribute', EntityType::class, [
                'class' => Attribute::class,
                'mapped' => false,
                'choice_label' => 'type',
            ])
            ->add('store', EntityType::class, [
                'class' => Store::class,
                'choice_label' => 'domain',
            ])
            ->add('image', FileType::class, [
                'required' => true,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'maxSizeMessage' => 'The file size is bigger then {{ limit }}',
                        'mimeTypes' => [
                            'image/jpeg',
                        ]
                    ])
                ]
            ])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
            'data_class' => Product::class
        ]);
    }
}
