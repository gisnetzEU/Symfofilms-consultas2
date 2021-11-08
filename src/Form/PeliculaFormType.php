<?php
namespace App\Form;

use App\Entity\Pelicula;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constrains\File;
use Symfony\Component\HttpFoundation\File\UploadFile;
use Symfony\Component\Filesystem\Filesystem;
//use Symfony\Component\HttpFoundation\File\File;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PeliculaFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $formulario = $builder
            ->add('titulo', TextType::class)
            ->add('duracion', NumberType::class, [
                'empty_data' => 0,
                'html5' => true,
            ])
            ->add('director', TextType::class)
            ->add('genero', TextType::class)
            ->add('sinopsis', TextareaType::class)
            ->add('estreno', NumberType::class)
            ->add('valoracion', NumberType::class)
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
            ->add('Guardar', SubmitType::class, [
                'attr' => ['class' => 'btn btn-success my-3']
            ]);
            //->getForm();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Pelicula::class,
        ]);
    }
}
