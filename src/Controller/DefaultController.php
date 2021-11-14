<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use App\Form\ContactoFormType;

class DefaultController extends AbstractController
{
    /**
     * Método index
     * @Route("/", name="portada")
     * @return \Symfony\Component\HttpFoundation\Response
     */
         
   /*  public function index(EntityManagerInterface $em){
      $peliculas = $em->createQuery(
          'SELECT p
          FROM App\Entity\Pelicula p
          WHERE p.caratula IS NOT NULL
          ORDER BY p.id ASC')           
      ->getResult();        

      $respuesta = implode('<br>', $peliculas);
      return new Response($respuesta);
      } */


      public function index():Response{
      return $this->render("portada.html.twig");
    }

    /**
     * @Route("/contact", name="contacto")
     */

    public function contacto(Request $request, MailerInterface $mailer):Response{

      //crea el formulario
      $formulario = $this->createForm(ContactoFormType::class);
      $formulario->handleRequest($request);

      //si el formulario fue enviado y es válido
      if($formulario->isSubmitted() && $formulario->isValid()){

        $datos = $formulario->getData();//recuperamos los datos del formulario
     
        //preparar el email
        $email = new TemplatedEmail();
        $email->from(new Address($datos['email'], $datos['nombre']))
          ->to($this->getParameter('app.admin_email'))
          ->subject($datos['asunto'])
          //template que usaremos para el email
          ->htmlTemplate('email/contact.html.twig')
          ->context([
            'de'=>$datos['email'],
            'nombre'=>$datos['nombre'],
            'asunto'=>$datos['asunto'],
            'mensaje'=>$datos['mensaje'],
          ]);

        //enviar el email
        $mailer->send($email);

        //flashear mensaje y redirigir a la portada
        $this->addFlash('success', 'Mensaje enviado correctamente');
        return $this->redirectToRoute('portada');
      }

      //muestra la vista con el formulario
      return $this->renderForm("contacto.html.twig", ["formulario"=>$formulario]);

  }
}