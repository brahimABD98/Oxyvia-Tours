<?php

namespace App\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use App\Entity\Client;
use App\Entity\Hotel;
use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ClassroomRepository;
use App\Repository\ClientRepository;
use App\Repository\HotelRepository;
use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/reservation")
 */
class ReservationController extends AbstractController
{
    public $res;
    /**
     * @Route("/", name="reservation_index", methods={"GET"})
     */
    public function index(ReservationRepository $reservationRepository): Response
    {
        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservationRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="reservation_new", methods={"GET","POST"})
     */
    public function new(Request $request,ReservationRepository $resRepo): Response
    {

//relation bin client w hotel => reservation
      //  $client= $clRepo->find(1);
       //$hotel=$hotRepo->find(1);
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        $var = $form->get('date_debut')->getData();


        if ($form->isSubmitted() && $form->isValid() ) {
            $entityManager = $this->getDoctrine()->getManager();
            $reservation->setType("hotel");
            $reservation->setPrix(20*$reservation->getNbPersonne());//i need prix nuit fel hotel *nb de nuit*nbpersonne
            $reservation->setCheckPayement("paye");
            $reservation->setClientId(1);
            $reservation->setHotelId(1);
            $entityManager->persist($reservation);
            $entityManager->flush();

            $data = $request->request->get('date_debut');
            var_dump($data['name']);

            return $this->RedirectToRoute('paiement1', array(
                'id' => $reservation->getId()
            ));
        }

        return $this->render('reservation/new.html.twig', [
            'reservation' => $reservation,
            'form' => $form->createView(),

        ]);
    }

    /**
     * @Route("/wiw/{id}", name="reservation_show", methods={"GET"})
     */
    public function show(Reservation $reservation): Response
    {
        return $this->render('reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }


    /**
     * @Route("Hotel/paiement/{id}", name="paiement1")
     */
    public function paiement1( $id ,ReservationRepository  $resRepo): Response
    {
        $res=$resRepo->find($id);


        return $this->render('cart1/index.html.twig', [
'id'=>$id,
'res'=>$res
        ]);
    }

    public function __construct()
    {
    }


    /**
     * @Route("/{id}/edit", name="reservation_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Reservation $reservation): Response
    {
        $form = $this->createForm(ReservationType::class, $reservation);
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

    /**
     * @Route("/{id}", name="reservation_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Reservation $reservation): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reservation->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($reservation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('reservation_index');
    }
}
