<?php

namespace App\Controller;
use App\Repository\ChambreRepository;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use App\Entity\Client;
use App\Entity\Hotel;
use App\Entity\Reservation;
use App\Form\ReservationType0;
use App\Repository\ClassroomRepository;
use App\Repository\ClientRepository;
use App\Repository\HotelRepository;
use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;

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
     * @Route("/new/{hotel_id}", name="reservation_new", methods={"GET","POST"})
     */
    public function new(Request $request,$hotel_id): Response
    {

        $defaultData = ['message' => 'Type your message here'];
        $form = $this->createFormBuilder($defaultData)
            ->add('date_debut', DateType::class, [
                'widget'=>'single_text',
                'attr' => ['class' => 'js-datepicker'],
                'data'          => new \DateTime(),
            ])

            ->add('date_fin', DateType::class, [
                'widget'=>'single_text',
                'attr' => ['class' => 'js-datepicker'],
                'data'          => new \DateTime(),
            ])
            ->add('nb_adulte')
            ->add('nb_enfants')
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid() ) {


            $date_debut = $form["date_debut"]->getData()->format('Y-m-d');
            $date_fin = $form["date_fin"]->getData()->format('Y-m-d');
            $nbadultes = $form["nb_adulte"]->getData();
            $nbenfants = $form["nb_enfants"]->getData();

            $name = $request->request->get("date_debut");
            return $this->RedirectToRoute('reservation_check', array(
                'hotel_id'=>$hotel_id,
                'date_debut' =>$date_debut,
                'date_fin' =>$date_fin,
                'nbadultes' =>$nbadultes,
                'nbenfants' =>$nbenfants,

            ));
        }

        return $this->render('reservation/new.html.twig', [

            'form' => $form->createView(),

        ]);
    }


    /**
     * @Route("/wiw/{date_debut}/{date_fin}/{nbadultes}/{nbenfants}", name="reservation_check")
     */
    public function ReservationCheck(HotelRepository $hotelRepository,ClientRepository $clientRepository,Request $request,$date_debut,$date_fin,$nbadultes,$nbenfants,ChambreRepository $chambreRepository): Response
    {
        $hotel_id = $_GET['hotel_id'];
        $reservation=new Reservation();
        $hotel=new Hotel();
        $nbchambreSingleDispo= $chambreRepository->NbChambreSingleDispo($hotel_id);
        $nbchambreDoubleDispo= $chambreRepository->NbChambreDoubleDispo($hotel_id);
        $client=$clientRepository->find(1);
        $hotel=$hotelRepository->find($hotel_id);
        $defaultData = ['message' => 'Type your message here'];
        $diff = date_diff(\DateTime::createFromFormat('Y-m-d', $date_debut),  \DateTime::createFromFormat('Y-m-d', $date_fin));
        $date_debut = $date_debut;
        $date_fin = $date_fin;
        $nbadultes = $nbadultes;
        $nbenfants = $nbenfants;
        $form = $this->createFormBuilder($defaultData)
            ->add('NbChambreSingleReserve')
            ->getForm();

        $form->handleRequest($request);

        //form 2 pour reservation room double
        $defaultData = ['message' => 'Type your message here'];
        $form2 = $this->createFormBuilder($defaultData)
            ->add('nbChambreDoubleReserve')
            ->getForm();
        $form2->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid() ) {
            $nbchambre = $form["NbChambreSingleReserve"]->getData();
            if ($nbchambre <= $nbchambreSingleDispo[0][1]) {
                $reservation->setCheckPayement("checked");
                $reservation->setDateDebut(\DateTime::createFromFormat('Y-m-d', $date_debut));
                $reservation->setDateFin(\DateTime::createFromFormat('Y-m-d', $date_fin));
                $reservation->setNbAdulte($nbadultes);
                $reservation->setNbEnfants($nbenfants);
                $reservation->setHotel($hotel);
                $reservation->setClient($client);
                $reservation->setType("reservation hotel");
                $reservation->setPrix($diff->d * $nbchambreSingleDispo['0']['0']->getPrix() * $nbchambre * ($nbadultes + $nbenfants));
                $reservation->setNbChambreSingleReserve($nbchambre);
                $reservation->setNbChambreDoubleReserve(0);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($reservation);
                $entityManager->flush();
                $chambreAaffecteRes= $chambreRepository->getChambresSingleWithLimit($hotel_id,$nbchambre);
                foreach ($chambreAaffecteRes as $res){
                    $room=$chambreRepository->find($res->getId());
                    $room->setReservation($reservation);
                    $room->setOccupe('occupe');
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($room);
                    $entityManager->flush();

                }
                return $this->redirectToRoute('reservation_index');
            }
        }


        else    if ($form2->isSubmitted() && $form2->isValid() ) {//form de double room
            $nbchambre2 = $form2["nbChambreDoubleReserve"]->getData();
            if($nbchambre2<=$nbchambreDoubleDispo[0][1]){
                $reservation->setCheckPayement("checked");
                $reservation->setDateDebut(\DateTime::createFromFormat('Y-m-d', $date_debut));
                $reservation->setDateFin(\DateTime::createFromFormat('Y-m-d', $date_fin));
                $reservation->setNbAdulte($nbadultes);
                $reservation->setNbEnfants($nbenfants);
                $reservation->setHotel($hotel);
                $reservation->setClient($client);
                $reservation->setType("reservation hotel");
                $reservation->setPrix($diff->d * $nbchambreDoubleDispo['0']['0']->getPrix() * $nbchambre2 * ($nbadultes + $nbenfants));
                $reservation->setNbChambreSingleReserve(0);
                $reservation->setNbChambreDoubleReserve($nbchambre2);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($reservation);
                $entityManager->flush();

               $chambreAaffecteRes= $chambreRepository->getChambresDoubleWithLimit($hotel_id,$nbchambre2);
               foreach ($chambreAaffecteRes as $res){
                   $room=$chambreRepository->find($res->getId());
                   $room->setReservation($reservation);
                   $room->setOccupe('occupe');
                   $entityManager = $this->getDoctrine()->getManager();
                   $entityManager->persist($room);
                   $entityManager->flush();

               }
                return $this->redirectToRoute('reservation_index');

            }

        }
        return $this->render('reservation/checkAvaibility.html.twig', [
            'hotel_id'=>$hotel_id,
            'nbchambreSingleDispo'=>$nbchambreSingleDispo,
            'nbchambreDoubleDispo'=>$nbchambreDoubleDispo,
            'form' => $form->createView(),
            'date_debut'=>$date_debut,
            'date_fin'=>$date_fin,
            'form2' => $form2->createView(),
            'nbadultes' => $nbadultes,
            'nbenfants' => $nbenfants

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
        $form = $this->createForm(ReservationType0::class, $reservation);
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
    public function delete(Request $request, Reservation $reservation ,ChambreRepository $chambreRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reservation->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();
            $chambres=$chambreRepository->deleteResFromChambre($reservation->getId());

            foreach ($chambres as $chambre){
                $chambre->setOccupe('non occupe');
                $chambre->setReservation(null);

            }
            $entityManager->remove($reservation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('gestion_reservation');
    }
}