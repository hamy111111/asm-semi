<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\File;
class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('price',TextType::class)
            ->add('size',TextType::class)
//            ->add('brand',TextType::class)
            ->add('brand',EntityType::class, array('class'=>'App\Entity\Brand','choice_label'=>"name"))
            ->add('category',EntityType::class, array('class'=>'App\Entity\Category','choice_label'=>"name"))
           ->add('image',FileType::class,[
               'label'=>"image file",
               'mapped'=>false,
               'required'=>false,
               'constraints'=>[
                  new File([
                        'maxSize'=>'1024K',
                       'mimeTypesMessage'=> 'Please upload a valid image'
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
