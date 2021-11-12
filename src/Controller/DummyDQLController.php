<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class DummyDQLController extends AbstractController
{
    
    /**
     *  @Route("/dummy/dql1")
     */
    public function dql1(EntityManagerInterface $em){
        $peliculas = $em->createQuery(
            'SELECT p
            FROM App\Entity\Pelicula p
            WHERE p.valoracion >3
            ORDER BY p.valoracion DESC')
        ->getResult();

        $respuesta = implode('<br>', $peliculas);
        return new Response($respuesta);
        
    }

    /**
     *  @Route("/dummy/dqlcampos")
     */
    public function dqlcampos(EntityManagerInterface $em){
        $peliculas = $em->createQuery(
            'SELECT p.titulo, p.estreno AS anyo
            FROM App\Entity\Pelicula p')
        ->getResult();

        dd($peliculas);
        return new Response('');
        
    }

    /**
     *  @Route("/dummy/dqllimit")
     */
    public function dqllimit(EntityManagerInterface $em){
        $peliculas = $em->createQuery(
            'SELECT p
            FROM App\Entity\Pelicula p
            ORDER BY p.id ASC')
        ->setMaxResults(5) //limit
        ->setFirstResult(0) //offset
        ->getResult();

        $respuesta = implode('<br>', $peliculas);
        return new Response($respuesta);        
    }

    /**
     *  @Route("/dummy/dqloperators")
     */
    public function dqloperators(EntityManagerInterface $em){
        $peliculas = $em->createQuery(
            'SELECT p
            FROM App\Entity\Pelicula p
            WHERE p.valoracion <= 3 AND p.estreno > 2000
                  OR p.titulo LIKE \'A%\'
            ORDER BY p.titulo ASC')   
        ->getResult();

        $respuesta = implode('<br>', $peliculas);
        return new Response($respuesta);        
    }

    /**
     *  @Route("/dummy/dqlbetween")
     */
    public function dqlBetween(EntityManagerInterface $em){
        $peliculas = $em->createQuery(
            'SELECT p
            FROM App\Entity\Pelicula p
            WHERE p.valoracion BETWEEN 2 AND 3')              
        ->getResult();

        $respuesta = implode('<br>', $peliculas);
        return new Response($respuesta);        
    }

    /**
     * @Route("/dummy/dqlbetween2")
     */
    public function dqlBetween2(EntityManagerInterface $em){
        $actores = $em->createQuery(
            'SELECT a
            FROM App\Entity\Actor a
            WHERE a.datadenaixement
                BETWEEN \'1960-01-01\' AND \'2030-12-31\'')
        ->getResult();

        $respuesta = implode('<br>', $actores);
        return new Response($respuesta);
    }

    /**
     * @Route("/dummy/dqlin")
     */
    public function dqlin(EntityManagerInterface $em){
        $actores = $em->createQuery(
            'SELECT a
            FROM App\Entity\Actor a
            WHERE a.nacionalitat IN( \'España\', \'mexicana\')')
        ->getResult();

        $respuesta = implode('<br>', $actores);
        return new Response($respuesta);
    }

     /**
     * @Route("/dummy/dqllike")
     */
    public function dqllike(EntityManagerInterface $em){
        $actores = $em->createQuery(
            'SELECT a
            FROM App\Entity\Actor a
            WHERE a.nom LIKE \'%lia%\' ')
        ->getResult();

        $respuesta = implode('<br>', $actores);
        return new Response($respuesta);
    }

    /**
     * @Route("/dummy/dqlisnull")
     */
    public function dqlisnull(EntityManagerInterface $em){
        $peliculas = $em->createQuery(
            'SELECT p
            FROM App\Entity\Pelicula p
            WHERE p.valoracion IS NULL
            ORDER BY p.titulo ASC')
        ->getResult();

        $respuesta = implode('<br>', $peliculas);
        return new Response($respuesta);
    }

    /**
     * @Route("/dummy/dql3/{titulo}")
     */
    public function dql3(string $titulo, EntityManagerInterface $em){
        //consulta con parámetros con nombre
        $peli = $em->createQuery(
            'SELECT p
            FROM App\Entity\Pelicula p
            WHERE p.titulo =:titulo')
            ->setParameter('titulo', $titulo)
            ->getSingleResult();

        return new Response($peli);
    }

    /**
     * @Route("/dummy/dql4/{valMin}/{valMax}")
     */
    public function dql4(int $valMin, int $valMax, EntityManagerInterface $em){
        //consulta con parámetros posicionales
        $peliculas = $em->createQuery(
            'SELECT p
            FROM App\Entity\Pelicula p
            WHERE p.valoracion BETWEEN ?1 AND ?2')
            ->setParameter(1, $valMin)
            ->setParameter(2, $valMax)
            ->getResult();

        $respuesta = implode('<br>', $peliculas);
        return new Response($respuesta);
    }

    /**
     * @Route("/dummy/dql5/{titulo}")
     */
    public function dql5(string $titulo, EntityManagerInterface $em){
        //consulta con operador LIKE
        $peliculas = $em->createQuery(
            'SELECT p
            FROM App\Entity\Pelicula p
            WHERE p.titulo LIKE :titulo')
            ->setParameter('titulo', "%$titulo%")
            ->getResult();

        $respuesta = implode('<br>', $peliculas);
        return new Response($respuesta);
    }

    /**
     * @Route("/dummy/dql6")
     */
    public function dql6(EntityManagerInterface $em){
        //consulta con distinct
        $generos = $em->createQuery(
            'SELECT DISTINCT p.genero
            FROM App\Entity\Pelicula p')            
            ->getResult();

        //el resultado llega como un array de arrays

        $resultado = '';
        
        foreach($generos as $genero)
          $resultado .= $genero['genero'].'<br>';
        return new Response($resultado);
    }

     /**
     * @Route("/dummy/dql7")
     */
    public function dql7(EntityManagerInterface $em){
        //consulta de totales (cuenta) con distinct
        //cuenta los distinto géneres de películas
        $generos = $em->createQuery(
            'SELECT COUNT(DISTINCT p.genero)
            FROM App\Entity\Pelicula p')            
            ->getSingleScalarResult();

        return new Response($generos);
    }

     /**
     * @Route("/dummy/dql8")
     */
    public function dql8(EntityManagerInterface $em){
        //campos calculados
        $peliculas = $em->createQuery(
            'SELECT p.titulo, p.valoracion*2 AS sobreDiez
            FROM App\Entity\Pelicula p
            ORDER BY sobreDiez DESC, p.titulo ASC')            
            ->getResult();
        
        //el resultado llega como un array de arrays
        $resultado = '';
        foreach($peliculas as $pelicula)
            $resultado .= implode(', ', $pelicula).'<br>';

        return new Response($resultado);
    }

    /**
     * @Route("/dummy/dql9/{longitud}")
     */
    public function dql9(int $longitud = 10, EntityManagerInterface $em){
        //campos calculados
        $peliculas = $em->createQuery(
            'SELECT UPPER(p.titulo) AS titulo
            FROM App\Entity\Pelicula p
            WHERE LENGTH(p.titulo) > :longitud
            ORDER BY titulo ASC')
            ->setParameter('longitud', $longitud)
            ->getResult();
        
        //el resultado llega como un array de arrays
        $resultado = '';
        foreach($peliculas as $pelicula)
            $resultado .= $pelicula['titulo'].'<br>';

        return new Response($resultado);
    }

    /**
     * @Route("/dummy/dql10")
     */
    public function dql10(EntityManagerInterface $em){
        //campos calculados
        $actores = $em->createQuery(
            'SELECT a.nom,
                    DATE_DIFF(CURRENT_DATE(), a.datadenaixement)/365 AS edad
            FROM App\Entity\Actor a
            ORDER BY edad DESC')           
            ->getResult();
        
        //el resultado llega como un array de arrays
        $resultado = '';
        foreach($actores as $actor)
            $resultado .= implode(' - ', $actor).'<br>';

        return new Response("Edad que tienen (si siguen vivos):<br>".$resultado);
    }

    /**
     * @Route("/dummy/dql11")
     */
    /* public function dql11(EntityManagerInterface $em){
        //campos calculados
        $peliculas = $em->createQuery(
            'SELECT p.titulo, YEAR(CURRENT_DATE())-p.estreno AS antiguedad
            FROM App\Entity\Pelicula p
            ORDER BY antiguedad DESC')           
        ->getResult();
        
        //el resultado llega como un array de arrays
        $resultado = '';
        foreach($peliculas as $pelicula)
            $resultado .= implode(' - ', $peliculas).'<br>';

        return new Response("Antiguedad de las pelis:<br>".$resultado);
    } */

}