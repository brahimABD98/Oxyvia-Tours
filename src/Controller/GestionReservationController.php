<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\ReservationTypeNbSingleRoom;
use App\Repository\ReservationRepository;
use App\Repository\VoyageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("dashboard")
 */

class GestionReservationController extends AbstractController
{
    /**
     * @Route("/gestion/reservation", name="gestion_reservation")
     */


    public function index(ReservationRepository $ResRepository): Response
    {
        return $this->render('reservation/index.html.twig', [

            'reservations' => $ResRepository->showReservationParClient(1),//find res pour client qui a id=1
        ]);
    }

    /**
     * @Route("/gestion/ToutReservations", name="Toutreservations")
     */


    public function AfficheToutReservation(ReservationRepository $ResRepository): Response
    {
        return $this->render('reservation/indexAllReservation.html.twig', [

            'reservations' => $ResRepository->findAll(),//find res pour client qui a id=1
        ]);
    }


    /**
     * @Route("/reservation/{id}", name="reservation_show1", methods={"GET"})
     */
    public function show(Reservation $reservation): Response
    {
        return $this->render('reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }




    /**
     * @Route("/reservation/{id}/edit", name="reservation_edit1", methods={"GET","POST"})
     */
    public function edit(Request $request, Reservation $reservation): Response
    {
        $form = $this->createForm(ReservationTypeNbSingleRoom::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('reservation_index');
        }

        return $this->render('reservation/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form->createView(),
        ]);
    }
}
