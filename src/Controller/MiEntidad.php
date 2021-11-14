<?php

namespace App\Entity;

class MiEntidad{
  //PROPIEDADES
  public $titulo, $valoracion;

  //CONSTRUCTOR
  public function __construct($t, $v){
    $this->titulo = $t;
    $this->valoracion = $v;
  }

  //toString
  public function __toString(){
    return $this->titulo.', valoración: '.
      ($this->valoracion ?? 'sin valorar');
  }
    
}