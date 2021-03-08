<?php

namespace App\Controller;

use App\Repository\ReclamationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("/dashboard")
 */
class BackofficeController extends AbstractController
{
    /**
     * @Route("/", name="backoffice")
     */
    public function index(): Response
    {
        return $this->render('backoffice/index.html.twig', [
            'controller_name' => 'BackofficeController',
        ]);
    }
    /**
     * @Route("/sav" ,name="sav",methods={"GET"})
     */
    public function sav(ReclamationRepository $reclamationRepository):Response
    {
        return $this->render('backoffice/sav.html.twig', ['reclamations' => $reclamationRepository->findAll(),

            ]);
    }
}
