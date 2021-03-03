<?php

namespace App\Controller;

use App\Entity\Hotel;
use App\Entity\Transport;
use App\Form\HotelType;
use App\Repository\HotelRepository;
use App\Repository\TransportRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class HotelController extends AbstractController
{
    /**
     * @Route("/hotel", name="hotel", methods={"GET"})
     */
    public function index(HotelRepository $hotelRepository): Response
    {
        return $this->render('front/index.html.twig', [
            'hotels' => $hotelRepository->findAll(),
        ]);
    }
    /**
     * @Route("/liste", name="liste")
     */
    public function trans(TransportRepository $transportRepository,HotelRepository $hotelRepository): Response
    {
        return $this->render('front/transportlist.html.twig', [
            'transports' => $transportRepository->findAll(),
        ]);
    }
    /**
     * @Route("/list", name="list")
     */
    public function index1(HotelRepository $hotelRepository): Response
    {
        return $this->render('front/hotels-list.html.twig', [
            'hotels' => $hotelRepository->findAll(),
        ]);
    }


    /**
     * @Route("/new", name="hotel_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $hotel = new Hotel();
        $form = $this->createForm(HotelType::class, $hotel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form['image']->getData();
            $filename = md5(uniqid()).'.'.$uploadedFile->guessExtension();
            $uploadedFile->move($this->getParameter('upload_directory'),$filename);
            $hotel->setImage($filename);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($hotel);
            $entityManager->flush();

            return $this->redirectToRoute('bookings');
        }

        return $this->render('hotel/new.html.twig', [
            'hotel' => $hotel,
            'form' => $form->createView(),
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
