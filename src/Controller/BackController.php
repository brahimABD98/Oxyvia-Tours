<?php

namespace App\Controller;

use App\Entity\Hotel;
use App\Entity\Transport;
use App\Form\HotelType;
use App\Form\TransportType;
use App\Repository\HotelRepository;
use App\Repository\TransportRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BackController extends AbstractController
{
    /**
     * @Route("/back", name="back")
     */
    public function index(): Response
    {
        return $this->render('back/index.html.twig', [
            'controller_name' => 'BackController',
        ]);
    }
    /**
     * @Route("/bookings", name="bookings", methods={"GET"})
     */
    public function index1(TransportRepository $transportRepository,HotelRepository $hotelRepository): Response
    {
        return $this->render('back/bookings.html.twig', [
            'transports' => $transportRepository->findAll(),
            'hotels' => $hotelRepository->findAll(),
        ]);
    }

    /**
     * @Route("/deletehotel/{id}", name="deletehotel")
     */
    public function delete($id)
    {
        $em=$this->getDoctrine()->getManager();
        $h=$em->getRepository(Hotel::class)->find($id);
        $em->remove($h);
        $em->flush();
        return $this->redirectToRoute("bookings");}

    /**
     * @Route("/deletetransport/{id}", name="deletetransport")
     */
    public function delete1($id)
    {
        $em=$this->getDoctrine()->getManager();
        $t=$em->getRepository(Transport::class)->find($id);
        $em->remove($t);
        $em->flush();
        return $this->redirectToRoute("bookings");}
    /**
     * @Route("/transport_new", name="transport_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $transport = new Transport();
        $form = $this->createForm(TransportType::class, $transport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form['image']->getData();
            $filename = md5(uniqid()).'.'.$uploadedFile->guessExtension();
            $uploadedFile->move($this->getParameter('upload_directory'),$filename);
            $transport->setImage($filename);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($transport);
            $entityManager->flush();

            return $this->redirectToRoute('bookings');
        }

        return $this->render('transport/new.html.twig', [
            'transport' => $transport,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/{id}/edit", name="hotel_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Hotel $hotel): Response
    {
        $form = $this->createForm(HotelType::class, $hotel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('bookings');
        }

        return $this->render('hotel/edit.html.twig', [
            'hotel' => $hotel,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit_transport", name="transport_edit", methods={"GET","POST"})
     */
    public function edit1(Request $request, Transport $transport): Response
    {
        $form = $this->createForm(TransportType::class, $transport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('bookings');
        }

        return $this->render('transport/edit.html.twig', [
            'transport' => $transport,
            'form' => $form->createView(),
        ]);
    }


}

