<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class FileService{
    //PROPIEDADES
    public $targetDirectory; //directorio de trabajo

    //CONSTRUCTOR
    //recibe el directorio sobre el que queremos trabajar
    public function __construct(String $targetDirectory){
        $this->targetDirectory = $targetDirectory;
    }

    //MÉTODOS
    public function upload(UploadedFile $file) : ?String{
        $extension = $file->guessExtension(); //calcula entensión
        $fichero = uniqid().".$extension"; //calcula nombre único

        try{
            //mueve el fichero a su ubicación final
            $file->move($this->targetDirectory, $fichero);

        // si no se pudo subir, retorna NULL
        }catch(FileException $e){
            return NULL;
        }

        //si todo fue correcto, retorna el nombre del fichero subido
        return $fichero;
    }

    //método para borrar ficheros
    public function delete(string $fichero){

        //borra el fichero indicado
        $fileSystem = new Filesystem();
        $fileSystem->remove("this->targetDirectory/$fichero");
    }

    //el método para reemplazar recibirá también el nombre del fichero que
    //queremos sustituir (puede ser string o NULL)

    public function replace(UploadedFile $file, ?string $anterior):String{

        //calculamos los datos del nuevo fichero
        $extension = $file->guessExtension(); //extension
        $fichero = uniqid().".$extension";  //nombre final del fichero

        try{
            //intenta mover el nuevo fichero
            $file->move($this->targetDirectory, $fichero);

            if($anterior){ //si había fichero anterior, bórralo
                $filesystem = new Filesystem();
                $filesystem->remove("this->targetDirectory/$anterior");
            }
        }catch(FileException $e){
            //si falló la subida del nuevo fichero
            return $anterior; //seguiremos usando el fichero anterior
        }

        //si el nuevo fichero se subió bien retorna su nombre
        return $fichero;        
    }
}