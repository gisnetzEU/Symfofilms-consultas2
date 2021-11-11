<?php
namespace App\Service;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\EntityManagerInterface;

class PaginatorService{

    //propiedades que necesitaré
    private $limit, $entityManager, $entityType = '', $total = 0;

    //CONSTRUCTOR
    //como usaré autowiring indicaré los valores por defecto también en services.yaml

    public function __construct(int $limit = 10, EntityManagerInterface $entityManager){
        $this->limit = $limit;
        $this->entityManager = $entityManager;
    }

    //SETTERS Y GETTERS
    //establece el tipo de entidad con el que va a trabajar el paginador

    public function setEntityType($entityType){
        $this->entityType = $entityType;
    }

    //recupera el total de resultados
    public function getTotal():int{
        return $this->total;
    }

    //recupera el total de páginas (resultados / límite)
    public function getTotalPages():int{
        return ceil($this->total / $this->limit);
    }

    //MÉTODOS
    //método que pagina los resultados
    public function paginate($dql, $page = 1):Paginator{
        $paginator = new Paginator($dql);  //crea el paginador a partir del DQL

        $paginator->getQuery()  //toma la consulta y...
          ->setFirstResult($this->limit * ($page -1))  //le añade el offset
          ->setMaxResults($this->limit);  //le añade el limit

        $this->total = $paginator->count(); //total de resultados

        return $paginator; //retorna el objeto Paginator
    }

    //método que recupera todas las entidades
    //podríamos tener otros métodos del estilo, para aplicar filtros y demás...
    public function findAllEntities(int $paginaActual = 1):Paginator{
        //preparamos la consulta usando DQL
        $consulta = $this->entityManager->createQuery(
            "SELECT p
            FROM $this->entityType p
            ORDER BY p.id DESC");   
        //retornamos los resultados paginados, llamando al método anterior
        return $this->paginate($consulta, $paginaActual);
    }
}
