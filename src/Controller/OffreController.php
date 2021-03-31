<?php

namespace App\Controller;

use App\Repository\OffresRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class OffreController extends AbstractController
{
    /**
     * @Route("/offre", name="offre")
     */
    public function index(): Response
    {
        return $this->render('offre/index.html.twig', [
            'controller_name' => 'OffreController',
        ]);
    }
    /**
     * @Route("/searchOffre", name="searchOffre")
     */
    public function searchOffre(Request $request,NormalizerInterface $Normalizer,OffresRepository $repository)
    {

        $requestString=$request->get('searchValue');
        $offres = $repository->search($requestString);
        $jsonContent = $Normalizer->normalize($offres,'json',['groups'=>'offre:read']);
        $retour=json_encode($jsonContent);

        return new Response($retour);

    }
}
