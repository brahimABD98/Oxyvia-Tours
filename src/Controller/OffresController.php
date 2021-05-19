<?php

namespace App\Controller;

use App\Entity\OffresTable;
use App\Repository\OffresRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OffresController extends AbstractController
{
    /**
     * @Route("/offres", name="offres")
     */
    public function index(): Response
    {
        return $this->render('offres/index.html.twig', [
            'controller_name' => 'OffresController',
        ]);
    }


    /**
     * @Route("/showOffres", name="showOffres", methods={"GET"})
     */
    public function showFront(): Response
    {
        $em=$this->getDoctrine()->getRepository(OffresTable::class);

        $list=$em->findAll();
        return $this->render('offres/showOffre.html.twig',["l"=>$list]);
    }

}
