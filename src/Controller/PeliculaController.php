<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Pelicula;
use App\Form\PeliculaFormType;
use App\Form\PeliculaDeleteFormType;
use App\Service\FileService;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;

class PeliculaController extends AbstractController
{
    /**
     * @Route("/peliculas", name="pelicula_list")
     */

    public function index(): Response
    {
        //recuperamos las pelis haciendo uso del repositorio PeliculaRepository,
        //haciendo uso del método findAll()
        $pelis = $this->getDoctrine()
            ->getRepository(Pelicula::class)
            ->findall();

        //cargamos la vista con el listado de películas y le pasamos las pelis recuperadas
        return $this->render('pelicula/list.html.twig', [
            'peliculas' => $pelis,
        ]);
    }

    /**
     * @Route("/pelicula/store", name="pelicula_store")
     */
    public function store(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $peli = new Pelicula();
        $peli->setTitulo('Avatar');
        $peli
            ->setDuracion(137)
            ->setDirector('Steven Spielberg')
            ->setGenero('Acción');

        $entityManager->persist($peli);
        $entityManager->flush();

        return new Response('Pelicula guardada con id ' . $peli->getId());
    }

      /**
     * @Route("/pelicula/create", name="pelicula_create", methods={"GET", "POST"})
     */
    public function create(Request $request, LoggerInterface $appInfoLogger, FileService $uploader): Response
    {
        $peli = new Pelicula();//crea el objeto de tipo Pelicula

        //crea el formulario
        $formulario = $this->createForm(PeliculaFormType::class, $peli);
        $formulario->handleRequest($request);        

        //si el formulario ha sido enviado y es válido
        if ($formulario->isSubmitted() && $formulario->isValid()) {

            //cambia el directorio configurado en services.yaml
            $uploader->targetDirectory = $this->getParameter('app.covers_root');
            
            //recuperación del fichero
            $file = $formulario->get('caratula')->getData();
            
            if($file) //si hay fichero...              
              $peli->setCaratula($uploader->upload($file)); //sube el fichero y asigna el nombre de la carátula a la peli

            //almacenar los datos de la peli en la BDD
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($peli);  //indica a doctrine que queremos guardar la peli
            $entityManager->flush(); //ejecuta las consultas

            //flashear el mensaje
            //flashear y logear el mensaje
            $mensaje = 'Película '.$peli->getTitulo().' guardada con id '.$peli->getId();
            $this->addFlash('success', $mensaje);
            $appInfoLogger->info($mensaje);      
       
            //redirige a la vista de detalles
            return $this->redirectToRoute('pelicula_show', ['id' => $peli->getId()]);
        }        

        //retornar la vista con el formulario
        return $this->renderForm('pelicula/create.html.twig', [
            'formulario' => $formulario]);
    }

    /**
     * @Route("/pelicula/{id<\d+>}", name="pelicula_show")
     */

    public function show(Pelicula $peli): Response
    {
        //retorna la respuesta (normalmente será una vista)
        return $this->render('pelicula/show.html.twig', ['pelicula' => $peli]);
    }

    /**
     * @Route("/pelicula/search/{campo}/{valor}", name="pelicula_search")
     */

    public function search($campo, $valor): Response
    {
        //recuperar las pelis
        $criterio = [$campo => $valor];
        $pelis = $this->getDoctrine()
            ->getRepository(Pelicula::class)
            ->findBy($criterio);

        //retorna la respuesta (normalmente será una vista)
        return new Response('Lista de pelis.<br>' . implode('<br>', $pelis));
    }

    /**
     * @Route("/pelicula/update/{id}")
     */

    public function update($id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $peli = $entityManager->getRepository(Pelicula::class)->find($id);

        //sino existe la peli lanzamos una excepción
        if (!$peli) {
            throw $this->createNotFoundException("No se encontró la peli $id");
        }

        $peli->setTitulo('Terminator 2 - Judgment Day'); //cambiamos el título
        $entityManager->flush(); //aplicamos los cambios

        //rederigimos el método show
        return $this->redirectToRoute('pelicula_show', ['id' => $id]);
    }

    /**
     * @Route("/pelicula/destroy/{id}")
     */

    public function destroy($id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $peli = $entityManager->getRepository(Pelicula::class)->find($id);

        //sino existe la peli lanzamos una excepción

        if (!$peli) {
            throw $this->createNotFoundException("No se encontró la peli $id");
        }

        $entityManager->remove($peli); //indica que hay que borrar la película
        $entityManager->flush(); //aplicamos los cambios

        //rederigimos el método show
        return new Response(
            "La pelicula <b>$peli</b> fue eliminada correctamente."
        );
    }

  

    /**
     * @Route("/pelicula/edit/{id<\d+>}", name="pelicula_edit", methods={"GET", "POST"})
     */

    public function edit(Pelicula $peli, Request $request,
        LoggerInterface $appInfoLogger, FileService $uploader): Response{

        $fichero = $peli->getCaratula(); //nombre del fichero original

        //crea el formulario
        $formulario = $this->createForm(PeliculaFormType::class, $peli);
        $formulario->handleRequest($request);

        //si el formulario fue enviado y es válido
        if ($formulario->isSubmitted() && $formulario->isValid()) {

            //crea una instancia de FileService (en lugar de inyectarla por parámetro)
            $uploader = new FileService($this->getParameter('app.covers_root'));

            //recuperación del nuevo fichero
            $file = $formulario->get('caratula')->getData();

            if($file) //si hay un nuevo fichero... 
                $fichero = $uploader->replace($file, $fichero);

            $peli->setCaratula($fichero); //actualizamos el campo carátula de la peli

            //guarda los cambios en la BDD
            $entityManager = $this->getDoctrine()->getManager();           
            $entityManager->flush(); //ejecuta las consultas

            //flashear el mensaje
            $mensaje = 'Película '.$peli->getTitulo().' actualizada correctamente.';
            $this->addFlash('success', $mensaje);
            $appInfoLogger->info($mensaje);

            //redirige a "ver detalles de la peli"
            return $this->redirectToRoute('pelicula_show', [
                'id' => $peli->getId()]);
        }

        //carga la vista con el formulario
        return $this->renderForm("pelicula/edit.html.twig", [
            "formulario" => $formulario,
            "pelicula" => $peli
        ]);
    }

    /**
     * @Route("/pelicula/delete/{id<\d+>}", name="pelicula_delete", methods={"GET", "POST"})
     */

    public function delete(Pelicula $peli, Request $request, LoggerInterface $appInfoLogger, FileService $uploader): Response
    {
        //creación del formulario de confirmación
        $formulario = $this->createForm(PeliculaDeleteFormType::class, $peli);
        $formulario->handleRequest($request);

        //si el formulario llega y es válido
        if ($formulario->isSubmitted() && $formulario->isValid()) {

            if($peli->getCaratula()) //si hay caratula
               $uploader->delete($peli->getCaratula()); //borra el fichero
            
            //borra la pelicula de la BDD
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($peli); //borra la película
            $entityManager->flush(); //aplicamos los cambios

            //flashear el mensaje
            $mensaje = 'Película '.$peli->getTitulo().' eliminada correctamente.';
            $this->addFlash('success', $mensaje);
            $appInfoLogger->info($mensaje);

            //redirige a la lista de peliculas
            return $this->redirectToRoute('pelicula_list');
        }

        //muestra el formulario de confirmación de borrado
        return $this->render('pelicula/delete.html.twig', [
            'formulario' => $formulario->createView(),
            'pelicula' => $peli,
        ]);
    }

    /**
     * @Route(
     *     "/pelicula/deleteimage/{id<\d+>}",
     *     name="pelicula_delete_cover",
     *     methods={"GET"}
     * )
     */

    public function deleteImage(Pelicula $peli, Request $request,
        FileService $uploader, EntityManagerInterface $em): Response{

        if($peli->getCaratula()){ //si hay carátula
            $uploader->delete($peli->getCaratula()); //borra el fichero

            $peli->setCaratula(NULL); //pone a null el campo en la BDD
            $em->flush(); //ejecuta las consultas

            $mensaje = 'Carátula de la película '.$peli->getTitulo().' borrada.';
            $this->addFlash('success', $mensaje);
        }
        //carga la vista con el formulario
        return $this->redirectToRoute('pelicula_edit', ['id' => $peli->getId()]);        
    }
}


