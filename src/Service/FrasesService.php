<?php
namespace App\Service;

class FrasesService{

  //método que retorna una frase aleatoria de película
  public function getFraseAleatoria(): string{
    //las mapeo en un array para el ejemplo, las podríamos 
    //tener también en BDD.

    $frases = [
      "Francamente, querida, me importa un bledo.",
      "Le haré una oferta que no podrá rechazar.",
      "Me gusta el olor a napalm por la mañana. ¡Huele a victoria!",
      "Luke, yo soy tu padre.",
      "Tócala otra vez, Sam.",
      "Totó, creo que ya no estamos en Kansas.",
      "Yo Tarzán, Tú Tarzán.",
      "Vamos a necesitar un barco más grande.",
      "Bond, James Bond.",
      "Bueno, nadie es perfecto.",
      "Louis, creo que éste es el inicio de una hermona amistad."
    ];

    return $frases[array_rand($frases)];
  }
}
