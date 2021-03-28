<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\EmailerAdminType;
use App\Form\ReservationType;
use App\Form\ReservationTypeNbSingleRoom;
use App\Repository\ChambreRepository;
use App\Repository\HotelRepository;
use App\Repository\ReservationRepository;
use App\Repository\VoyageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Bridge\Google\Transport\GmailSmtpTransport;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Validator\Mapping\Loader\LoaderInterface;


use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

use Symfony\Component\Mailer\Bridge\Google\Smtp\GmailTransport;
use Symfony\Component\Mailer\Mailer;
/**
 * @Route("dashboard")
 */

class GestionReservationController extends AbstractController
{
    /**
     * @Route("/gestion/reservation", name="gestion_reservation")
     */


    public function index(ReservationRepository $ResRepository,Request $request): Response
    {
        $limit = 4;
        $filters=$request->get('hotel');
        $filtersType=$request->get('type');

        $page = (int)$request->query->get("page", 1);
        $reservations = $ResRepository->getPaginatedResPerClient(1,$page, $limit,$filters,$filtersType);
     //   dd($reservations);
        $total=count($ResRepository->getTotalResPerClient(1,$filters,$filtersType));
        if($request->get('ajax')){
            return new JsonResponse([
                'content' => $this->renderView('reservation/contentResPerClient.html.twig',
                    compact('reservations', 'total', 'limit', 'page'))
            ]);
        }


        return $this->render('reservation/index.html.twig',compact('reservations','limit','page',
            'total'));
    }

    /**
     * @Route("/gestion/ToutReservations", name="Toutreservations")
     */


    public function AfficheToutReservation(ReservationRepository $ResRepository,Request $request): Response
    {
        $limit = 4;
        $filters=$request->get('hotel');
        $filtersType=$request->get('type');
        $page = (int)$request->query->get("page", 1);
        $reservations = $ResRepository->getPaginatedRes($page, $limit,$filters,$filtersType);


        $total=count($ResRepository->getTotalReservation($filters,$filtersType));
        if($request->get('ajax')){
            return new JsonResponse([
                'content' => $this->renderView('reservation/content.html.twig',
                    compact('reservations', 'total', 'limit', 'page'))
            ]);
        }
        return $this->render('reservation/indexAllReservation.html.twig', [

            'reservations' =>$reservations,
            'limit'=>$limit,
            'page'=>$page,
            'total'=>$total

            //find res pour client qui a id=1
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
     * @Route("/gestion/reservation/stats", name="hotelstats", methods={"GET","POST"})
     */
    public function hotelStats(Request $request,HotelRepository  $hotelRepository,ReservationRepository  $reservationRepository): Response
    {
        $totperMois=$reservationRepository->TotalPrixPerMonth();
        $nbMois=[];
        $prixtotal=[];
       // dd($totperMois);
        $max = sizeof($totperMois);
        for($i = 0; $i < $max;$i++)
        {
            array_push($nbMois, $totperMois[$i]['mois']);
            array_push($prixtotal, $totperMois[$i]['prix']);

        }



        $lstRes=$hotelRepository->findAll();
        $hotelNom=[];
        $resCount=[];
        foreach ($lstRes as $res){

            $hotelNom[]=$res->getName();
            $resCount[]=count($res->getReservation());
        }

        return $this->render('reservation/MostResHotelStats.html.twig', [
            'hotelNom' => json_encode($hotelNom),
            'resCount' =>json_encode($resCount),
            'nbMois'=>json_encode($nbMois),
            'prixtotal'=>json_encode($prixtotal),
        ]);
    }



}
