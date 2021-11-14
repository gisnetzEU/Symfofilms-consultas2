<?php

namespace App\Form;

use App\Entity\Actor;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class ActorFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $formulario = $builder
            ->add('nom', TextType::class)
            ->add('datadenaixement', DateType::class, [
                'empty_data' => 0,
                'html5' => true,
            ])
            ->add('nacionalitat', TextType::class)
            ->add('biografia', TextareaType::class)
            ->add('caratula', FileType::class, [
                'required' => false,
                'data_class' => NULL,
                'constraints' => [
                    new File([
                        'maxSize' =>'10240k',
                        'mimeTypes' => ['image/jpeg', 'image/png','image/gif'],
                        'mimeTypesMessage' => 'Debes subir una imagen png, jpg o gif'
                    ])
                ]
            ])            
            ->add('Actualizar', SubmitType::class, [
                'attr' => ['class' => 'btn btn-success my-3'],
            ])
            ->getForm();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Actor::class,
        ]);
    }
}
