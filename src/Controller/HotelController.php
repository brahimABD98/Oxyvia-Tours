<?php

namespace App\Controller;

use App\Entity\Hotel;
use App\Entity\Transport;
use App\Form\HotelType;
use App\Entity\Chambre;
use App\Form\ChambreType;
use App\Repository\HotelRepository;
use App\Repository\TransportRepository;
use App\Repository\ChambreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("/hotel", name="hotel", methods={"GET"})
 */

class HotelController extends AbstractController
{
    /**
     * @Route("/", name="hotel", methods={"GET"})
     */
    public function index(HotelRepository $hotelRepository): Response
    {
        return $this->render('front/index.html.twig', [
            'hotels' => $hotelRepository->findAll(),
        ]);
    }






    /**
     * @Route("/{id}", name="hotel_show", methods={"GET"})
     */
    public function show(Hotel $hotel): Response
    {
        return $this->render('hotel/show.html.twig', [
            'hotel' => $hotel,
        ]);

    }




}
