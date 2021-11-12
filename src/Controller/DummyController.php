<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DummyController extends AbstractController
{
    
    /**
     *  @Route("/dummy/hola", name="dummy_saluda", priority=1)
     */
    public function saluda(): Response{
        return new Response("Hola");
    }
    
}
