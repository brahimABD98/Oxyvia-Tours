<?php

namespace App\Controller;
use App\Entity\Voyage;
use App\Repository\ChambreRepository;
use App\Repository\PlaceRepository;
use App\Repository\TransportRepository;
use App\Repository\VoyageRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use http\Message;

use MercurySeries\FlashyBundle\FlashyNotifier;
use phpDocumentor\Reflection\DocBlock\Serializer;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\HttpFoundation\JsonResponse;
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
use Symfony\Component\Validator\Constraints\DateTime;

use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Email;


use Symfony\Component\Serializer\SerializerInterface;

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
     * @Route("/hotel/{hotel_id}", name="reservation_newhotel")
     */
    public function new(Request $request, $hotel_id,HotelRepository $hotelRepository): Response
    {

        $defaultData = ['message' => 'Type your message here'];
        $form = $this->createFormBuilder($defaultData)
            ->add('date_debut', DateType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'js-datepicker'],
                'data' => new \DateTime(),
            ])
            ->add('date_fin', DateType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'js-datepicker'],
                'data' => new \DateTime(),
            ])
            ->add('nb_adulte', IntegerType::class, [
                'attr' => array('value' => '1'),
                'constraints' => new NotBlank()

            ])
            ->add('nb_enfants', IntegerType::class, [
                'attr' => array('value' => '0'),
                'constraints' => new NotBlank(),

            ])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $nb_enf = $form->getData()['nb_enfants'];
            $nb_adul = $form->getData()['nb_adulte'];


            $date_debut = $form["date_debut"]->getData()->format('Y-m-d');
            $date_fin = $form["date_fin"]->getData()->format('Y-m-d');
            $nbadultes = $form["nb_adulte"]->getData();
            $nbenfants = $form["nb_enfants"]->getData();

            $name = $request->request->get("date_debut");
            return $this->RedirectToRoute('reservation_check', array(
                'hotel_id' => $hotel_id,
                'date_debut' => $date_debut,
                'date_fin' => $date_fin,
                'nbadultes' => $nbadultes,
                'nbenfants' => $nbenfants,

            ));
        }
        $ho = $hotelRepository->find($hotel_id);

        return $this->render('reservation/new.html.twig', [

            'form' => $form->createView(),
            'ho'=>$ho

        ]);
    }

    ///reservation voyage


    /**
     * @Route("/transport/{transport_id}", name="reservation_newTransport")
     */
    public function newResTransport(MailerInterface $mailer,ClientRepository $clientRepository,Request $request, $transport_id,HotelRepository $hotelRepository,TransportRepository $transportRepository): Response
    {

        $transport=$transportRepository->find($transport_id);
        $client=$clientRepository->find(1);

        $defaultData = ['message' => 'Type your message here'];
        $form = $this->createFormBuilder($defaultData)
            ->add('date_debut', DateType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'js-datepicker'],
                'data' => new \DateTime(),
            ])
            ->add('date_fin', DateType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'js-datepicker'],
                'data' => new \DateTime(),
            ])

            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $date_debut = $form["date_debut"]->getData()->format('Y-m-d');
            $date_fin = $form["date_fin"]->getData()->format('Y-m-d');

            $name = $request->request->get("date_debut");
            $reservation=new Reservation();

            $reservation->setToken($this->generateToken());
            $reservation->setConfirme("non confirme");
            $reservation->setDateDebut(\DateTime::createFromFormat('Y-m-d', $date_debut));
            $reservation->setDateFin(\DateTime::createFromFormat('Y-m-d', $date_fin));
            $reservation->setNbAdulte(0);
            $reservation->setNbEnfants(0);
            $reservation->setHotel(null);
            $reservation->setClient($client);
            $reservation->setVoyage(null);
            $reservation->setTransport($transport);
            $reservation->setType("reservation transport");
            $diff = date_diff(\DateTime::createFromFormat('Y-m-d', $date_debut), \DateTime::createFromFormat('Y-m-d', $date_fin));

            if ($diff->d == 0) {
                $reservation->setPrix(1 *$transport->getPrix() );

            } else {
                $reservation->setPrix($diff->d *$transport->getPrix() );

            }

            $reservation->setNbChambreSingleReserve(0);
            $reservation->setNbChambreDoubleReserve(0);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($reservation);
            $entityManager->flush();


            $email = (new TemplatedEmail())
                ->from('saieftaher1@gmail.com')
                ->to('saieftaher1@gmail.com')
                ->subject('confirmation de votre réservation!')
                ->htmlTemplate('reservation/ConfirmationReservationVoyage.html.twig')
                ->context([
                    'client' => $reservation->getClient()->getNom(),
                    'date_debut' => $reservation->getDateDebut(),
                    'date_fin' => $reservation->getDateFin(),

                    'total' => $reservation->getPrix(),
                    'token' => $reservation->getToken(),

                ]);
            $mailer->send($email);



        }

        return $this->render('reservation/ReservationTransport.html.twig', [

            'form' => $form->createView(),
            'trans'=>$transport

        ]);
    }
















    /////////RESERVATION VOYAGE////////////

    /**
     * @Route("/voyage/{voy_id}", name="reservation_newvoy", methods={"GET","POST"})
     */
    public function newReservationVoyage($voy_id,Request $request, VoyageRepository $voyageRepository, PlaceRepository $placeRepository): Response
    {

        $voy = $voyageRepository->find($voy_id);
        $placepervoy = $voy->getPlace()->toArray();

        $form = $this->createFormBuilder()
            ->add('nbDePersonne', IntegerType::class, [

                'attr' => array(
                    'placeholder' => 'Nombre de Personne'
                ),
                    'constraints' => [
                            new NotBlank(),

                        ],
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->RedirectToRoute('paiement1', array(
                'voy' => $voy->getId(),
                'nb'=>$form["nbDePersonne"]->getData()
            ));

        }
        return $this->render('voyage/ReservationVoyage.html.twig', [
            'placepervoy' => $placepervoy,
            'voy' => $voy,
            'idvoy'=>$voy_id,
            'form' => $form->createView(),

        ]);
    }


    /**
     * @Route("/wiw/{date_debut}/{date_fin}/{nbadultes}/{nbenfants}", name="reservation_check")
     */
    public function ReservationCheck(MailerInterface $mailer, ReservationRepository $reservationRepository, HotelRepository $hotelRepository, ClientRepository $clientRepository, Request $request, $date_debut, $date_fin, $nbadultes, $nbenfants, ChambreRepository $chambreRepository): Response
    {
        $hotel_id = $_GET['hotel_id'];
        $reservation = new Reservation();
        $hotel = new Hotel();
        $nbchambreSingleDispo = $chambreRepository->NbChambreSingleDispo($hotel_id);
        $nbchambreDoubleDispo = $chambreRepository->NbChambreDoubleDispo($hotel_id);
        $client = $clientRepository->find(1);
        $hotel = $hotelRepository->find($hotel_id);
        $defaultData = ['message' => 'Type your message here'];
        $diff = date_diff(\DateTime::createFromFormat('Y-m-d', $date_debut), \DateTime::createFromFormat('Y-m-d', $date_fin));
        $date_debut = $date_debut;
        $date_fin = $date_fin;
        $nbadultes = $nbadultes;
        $nbenfants = $nbenfants;
        $form = $this->createFormBuilder($defaultData)
            ->add('NbChambreSingleReserve', IntegerType::class, [
                'attr' => array('value' => '0'),
            ])
            ->add('nbChambreDoubleReserve', IntegerType::class, [
                'attr' => array('value' => '0'),
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $nbchambre = $form["NbChambreSingleReserve"]->getData();
            $nbchambre2 = $form["nbChambreDoubleReserve"]->getData();

            if ($nbchambre != 0 && $nbchambre2 != 0) {
                if ($nbchambre <= $nbchambreSingleDispo[0][1] && $nbchambre2 <= $nbchambreDoubleDispo[0][1]) {

                    $reservation->setToken($this->generateToken());
                    $reservation->setConfirme("non confirme");
                    $reservation->setDateDebut(\DateTime::createFromFormat('Y-m-d', $date_debut));
                    $reservation->setDateFin(\DateTime::createFromFormat('Y-m-d', $date_fin));
                    $reservation->setNbAdulte($nbadultes);
                    $reservation->setNbEnfants($nbenfants);
                    $reservation->setHotel($hotel);
                    $reservation->setClient($client);
                    $reservation->setVoyage(null);
                    $reservation->setTransport(null);

                    $reservation->setType("reservation hotel");
                    if ($diff->d == 0) {
                        $reservation->setPrix(1 * (($nbchambreDoubleDispo['0']['0']->getPrix() * $nbchambre2) + ($nbchambreSingleDispo['0']['0']->getPrix() * $nbchambre)));

                    } else {
                        $reservation->setPrix($diff->d * (($nbchambreDoubleDispo['0']['0']->getPrix() * $nbchambre2) + ($nbchambreSingleDispo['0']['0']->getPrix() * $nbchambre)));

                    }

                    $reservation->setNbChambreSingleReserve($nbchambre);
                    $reservation->setNbChambreDoubleReserve($nbchambre2);
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($reservation);
                    $entityManager->flush();

                    $chambreAaffecteRes = $chambreRepository->getChambresDoubleWithLimit($hotel_id, $nbchambre2);
                    foreach ($chambreAaffecteRes as $res) {
                        $room = $chambreRepository->find($res->getId());
                        $room->setReservation($reservation);
                        $room->setOccupe('occupe');
                        $entityManager = $this->getDoctrine()->getManager();
                        $entityManager->persist($room);
                        $entityManager->flush();
                    }

                    $chambreAaffecteRes = $chambreRepository->getChambresSingleWithLimit($hotel_id, $nbchambre);
                    foreach ($chambreAaffecteRes as $res) {
                        $room = $chambreRepository->find($res->getId());
                        $room->setReservation($reservation);
                        $room->setOccupe('occupe');
                        $entityManager = $this->getDoctrine()->getManager();
                        $entityManager->persist($room);
                        $entityManager->flush();
                    }

                    //////////SEND EMAIL FOR CONFIRMATION

                    $email = (new TemplatedEmail())
                        ->from('saieftaher1@gmail.com')
                        ->to('saieftaher1@gmail.com')
                        ->subject('confirmation de votre réservation!')
                        ->htmlTemplate('reservation/confirmReservationEmail.html.twig')
                        ->context([
                            'client' => $reservation->getClient()->getNom(),
                            'hotel' => $reservation->getHotel()->getName(),
                            'date_debut' => $reservation->getDateDebut(),
                            'date_fin' => $reservation->getDateFin(),
                            'nbadulte' => $reservation->getNbAdulte(),
                            'nbenfants' => $reservation->getNbEnfants(),
                            'total' => $reservation->getPrix(),
                            'token' => $reservation->getToken(),
                            'nbsingle' => $reservation->getNbChambreSingleReserve(),
                            'nbdouble' => $reservation->getNbChambreDoubleReserve()

                        ]);
                    $mailer->send($email);

                    $this->addFlash("info", "un email de confirmation a été envoyé a votre boite mail !");


                }


            } else if ($nbchambre != 0) {
                if ($nbchambre <= $nbchambreSingleDispo[0][1]) {
                    $reservation->setToken($this->generateToken());

                    $reservation->setConfirme("non confirme");
                    $reservation->setDateDebut(\DateTime::createFromFormat('Y-m-d', $date_debut));
                    $reservation->setDateFin(\DateTime::createFromFormat('Y-m-d', $date_fin));
                    $reservation->setNbAdulte($nbadultes);
                    $reservation->setNbEnfants($nbenfants);
                    $reservation->setHotel($hotel);
                    $reservation->setVoyage(null);
                    $reservation->setClient($client);
                    $reservation->setTransport(null);

                    $reservation->setType("reservation hotel");

                    if ($diff->d == 0) {
                        $reservation->setPrix(1 * $nbchambreSingleDispo['0']['0']->getPrix() * $nbchambre);

                    } else {
                        $reservation->setPrix($diff->d * $nbchambreSingleDispo['0']['0']->getPrix() * $nbchambre);

                    }

                    $reservation->setNbChambreSingleReserve($nbchambre);
                    $reservation->setNbChambreDoubleReserve(0);
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($reservation);
                    $entityManager->flush();
                    $chambreAaffecteRes = $chambreRepository->getChambresSingleWithLimit($hotel_id, $nbchambre);
                    foreach ($chambreAaffecteRes as $res) {
                        $room = $chambreRepository->find($res->getId());
                        $room->setReservation($reservation);
                        $room->setOccupe('occupe');
                        $entityManager = $this->getDoctrine()->getManager();
                        $entityManager->persist($room);
                        $entityManager->flush();

                    }

                    //////////SEND EMAIL FOR CONFIRMATION

                    $email = (new TemplatedEmail())
                        ->from('saieftaher1@gmail.com')
                        ->to('saieftaher1@gmail.com')
                        ->subject('confirmation de votre réservation!')
                        ->htmlTemplate('reservation/confirmReservationEmail.html.twig')
                        ->context([
                            'client' => $reservation->getClient()->getNom(),
                            'hotel' => $reservation->getHotel()->getName(),
                            'date_debut' => $reservation->getDateDebut(),
                            'date_fin' => $reservation->getDateFin(),
                            'nbadulte' => $reservation->getNbAdulte(),
                            'nbenfants' => $reservation->getNbEnfants(),
                            'total' => $reservation->getPrix(),
                            'token' => $reservation->getToken(),
                            'nbsingle' => $reservation->getNbChambreSingleReserve(),
                            'nbdouble' => 0
                        ]);
                    $mailer->send($email);
                    $this->addFlash("info", "un email de confirmation a été envoyé a votre boite mail !");

                }

            } else if ($nbchambre2 != 0) {
                if ($nbchambre2 <= $nbchambreDoubleDispo[0][1]) {

                    $reservation->setToken($this->generateToken());
                    $reservation->setConfirme("non confirme");
                    $reservation->setDateDebut(\DateTime::createFromFormat('Y-m-d', $date_debut));
                    $reservation->setDateFin(\DateTime::createFromFormat('Y-m-d', $date_fin));
                    $reservation->setNbAdulte($nbadultes);
                    $reservation->setNbEnfants($nbenfants);
                    $reservation->setHotel($hotel);
                    $reservation->setVoyage(null);
                    $reservation->setClient($client);
                    $reservation->setTransport(null);

                    $reservation->setType("reservation hotel");

                    if ($diff->d == 0) {
                        $reservation->setPrix(1 * $nbchambreDoubleDispo['0']['0']->getPrix() * $nbchambre2);

                    } else {
                        $reservation->setPrix($diff->d * $nbchambreDoubleDispo['0']['0']->getPrix() * $nbchambre2);

                    }

                    $reservation->setNbChambreSingleReserve(0);
                    $reservation->setNbChambreDoubleReserve($nbchambre2);
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($reservation);
                    $entityManager->flush();

                    $chambreAaffecteRes = $chambreRepository->getChambresDoubleWithLimit($hotel_id, $nbchambre2);
                    foreach ($chambreAaffecteRes as $res) {
                        $room = $chambreRepository->find($res->getId());
                        $room->setReservation($reservation);
                        $room->setOccupe('occupe');
                        $entityManager = $this->getDoctrine()->getManager();
                        $entityManager->persist($room);
                        $entityManager->flush();

                    }

                    //////////SEND EMAIL FOR CONFIRMATION

                    $email = (new TemplatedEmail())
                        ->from('saieftaher1@gmail.com')
                        ->to('saieftaher1@gmail.com')
                        ->subject('confirmation de votre réservation!')
                        ->htmlTemplate('reservation/confirmReservationEmail.html.twig')
                        ->context([
                            'client' => $reservation->getClient()->getNom(),
                            'hotel' => $reservation->getHotel()->getName(),
                            'date_debut' => $reservation->getDateDebut(),
                            'date_fin' => $reservation->getDateFin(),
                            'nbadulte' => $reservation->getNbAdulte(),
                            'nbenfants' => $reservation->getNbEnfants(),
                            'total' => $reservation->getPrix(),
                            'token' => $reservation->getToken(),
                            'nbsingle' => 0,
                            'nbdouble' => $reservation->getNbChambreDoubleReserve()
                        ]);
                    $mailer->send($email);
                    $this->addFlash("info", "un email de confirmation a été envoyé a votre boite mail !");

                }


            } else if ($nbchambre == 00 && $nbchambre2 == 0) {
                $this->addFlash('warning', 'vous devez réserver au moins une chambre');

            }

        }


        return $this->render('reservation/checkAvaibility.html.twig', [
            'hotel_id' => $hotel_id,
            'nbchambreSingleDispo' => $nbchambreSingleDispo,
            'nbchambreDoubleDispo' => $nbchambreDoubleDispo,
            'form' => $form->createView(),
            'date_debut' => $date_debut,
            'date_fin' => $date_fin,
            'nbadultes' => $nbadultes,
            'nbenfants' => $nbenfants

        ]);
    }

    /**
     * @Route("/confirmer-mon-compte/{token}", name="confirm_reservation")
     * @param string $token
     */
    public function confirmReservation(string $token, ReservationRepository $reservationRepository)
    {
        $res = $reservationRepository->findOneBy(["token" => $token]);

                $res->setToken("");
                $res->setConfirme('confirme');
                $em = $this->getDoctrine()->getManager();
                $em->persist($res);
                $em->flush();
                $this->addFlash("info", "Merci pour votre confiance , la reservation a été confirmé avec sucées !");
            return $this->redirectToRoute("ajout", array('res' => $res->getPrix()));


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
     * @Route("voyage/confirmation/{voy}/{nb}", name="paiement1")
     */
    public function paiement1(MailerInterface $mailer,ClientRepository $clientRepository,Request $request,ChambreRepository $chambreRepository,HotelRepository $hotelRepository,$voy,$nb, ReservationRepository $resRepo, VoyageRepository $voyageRepository): Response
    {
        $voy = $voyageRepository->find($voy);
        $reservation=new Reservation();
       // dd($voy);

        $hotel=$voy->getHotel()->getId();
        $diffJours = date_diff($voy->getDateDebut(), $voy->getDateFin())->d;
         $nbchambreSingleDispo = $chambreRepository->NbChambreSingleDispo($hotel);
        $getChambreSinglePrixPerHotel=$chambreRepository->getChambreSinglePrixPerHotel($hotel);

        $prixTransport=$voy->getTransport()->toArray()[0]->getPrix();
        $total=$getChambreSinglePrixPerHotel[0]['prix']*$diffJours+$prixTransport+$voy->getPrixPersonne()*$nb;

        $form = $this->createFormBuilder()

            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

        $reservation->setToken($this->generateToken());
        $reservation->setConfirme("non confirme");
        $reservation->setDateDebut($voy->getDateDebut());
        $reservation->setDateFin($voy->date_fin);
        $reservation->setNbAdulte($nb);
        $reservation->setNbEnfants(0);
        $reservation->setHotel($voy->getHotel());
        $reservation->setClient($clientRepository->find(1));
            $reservation->setTransport(null);

            $reservation->setVoyage($voy);

        $reservation->setType("reservation voyage");
        if ($diffJours == 0) {
            $reservation->setPrix($getChambreSinglePrixPerHotel[0]['prix']*1+$prixTransport+$voy->getPrixPersonne()*$nb);

        } else {
            $reservation->setPrix($getChambreSinglePrixPerHotel[0]['prix']*$diffJours+$prixTransport+$voy->getPrixPersonne()*$nb);

        }

        $reservation->setNbChambreSingleReserve($nb);
        $reservation->setNbChambreDoubleReserve(0);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($reservation);
        $entityManager->flush();

        $chambreAaffecteRes = $chambreRepository->getChambresSingleWithLimit($voy->getHotel()->getId(), $nb);

        foreach ($chambreAaffecteRes as $res) {
            $room = $chambreRepository->find($res->getId());
    
            $room->setReservation($reservation);
            $room->setOccupe('occupe');
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($room);
            $entityManager->flush();
        }

            $email = (new TemplatedEmail())
                ->from('saieftaher1@gmail.com')
                ->to('saieftaher1@gmail.com')
                ->subject('confirmation de votre réservation!')
                ->htmlTemplate('reservation/ConfirmationReservationVoyage.html.twig')
            ->context([
                    'client' => $reservation->getClient()->getNom(),
                    'hotel' => $reservation->getHotel()->getName(),
                    'date_debut' => $reservation->getDateDebut(),
                    'date_fin' => $reservation->getDateFin(),
                    'nbadulte' => $reservation->getNbAdulte(),
                    'nbenfants' => $reservation->getNbEnfants(),
                    'total' => $reservation->getPrix(),
                    'token' => $reservation->getToken(),
                    'nbsingle' => 0,
                    'nbdouble' => $reservation->getNbChambreSingleReserve()
                ]);
            $mailer->send($email);
            $this->addFlash("info", "un email de confirmation a été envoyé a votre boite mail !");

        }

        return $this->render('cart1/index.html.twig', [
            "voy" => $voy,
            "nb"=>$nb,
            'diff'=>$diffJours,
            'total'=>$total,
            "getChambreSinglePrixPerHotel"=>$getChambreSinglePrixPerHotel[0],
            "transport"=>$voy->getTransport()->toArray(),
            'form' => $form->createView(),

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
    public function delete(Request $request, Reservation $reservation, ChambreRepository $chambreRepository): Response
    {

        if ($this->isCsrfTokenValid('delete' . $reservation->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();
            $chambres = $chambreRepository->deleteResFromChambre($reservation->getId());

            foreach ($chambres as $chambre) {
                $chambre->setOccupe('non occupe');
                $chambre->setReservation(null);

            }
            $entityManager->remove($reservation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('gestion_reservation');
    }


    /**
     * @Route("/users/data/download", name="usersDataDownload")
     */
    public function usersDataDownload(VoyageRepository  $voyageRepository,ReservationRepository $reservationRepository)
    {
        // On définit les options du PDF
        $pdfOptions = new Options();
        // Police par défaut
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->setIsRemoteEnabled(true);

        // On instancie Dompdf
        $dompdf = new Dompdf($pdfOptions);
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => FALSE,
                'verify_peer_name' => FALSE,
                'allow_self_signed' => TRUE
            ]
        ]);
        $dompdf->setHttpContext($context);

        //lst voy
        $lst=$reservationRepository->findAll();


        // On génère le html
        $html = $this->renderView('reservation/ResDatapdf.html.twig',[
            'list'=>$lst
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // On génère un nom de fichier
        $fichier = 'voyage-data'.$this->generateToken2().'.pdf';

        // On envoie le PDF au navigateur
        $dompdf->stream($fichier, [
            'Attachment' => true
        ]);
        return new Response('', 200, [
            'Content-Type' => 'application/pdf',
        ]);

    }




    /**
     * @return string
     * @throws \Exception
     */
    private function generateToken()
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }
    /**
     * @return string
     * @throws \Exception
     */
    private function generateToken2()
    {
        return rtrim(strtr(base64_encode(random_bytes(5)), '+/', '-_'), '=');
    }



    /**
     * @Route("/wiw/addresapi", name="AjoutReservationVoyageapi")
     */
    public function AjoutReservationVoyageapi(SerializerInterface $serializer,VoyageRepository $voyageRepository,ReservationRepository $reservationRepository, HotelRepository $hotelRepository, ClientRepository $clientRepository, Request $request, ChambreRepository $chambreRepository): Response
    {


        $date_debut = $request->query->get("date_debut");
        $date_fin = $request->query->get("date_fin");

        $nbadultes = $request->query->get("nbadultes");
        $nbenfants = $request->query->get("nbenfants");
        $nbsingle = $request->query->get("nbsingle");
        $nbdouble = $request->query->get("nbdouble");
        $voyage_id = $request->query->get("voyage_id");

        $nb=$nbadultes+$nbenfants;

       // $voyage = new Voyage();

       // $nbchambreDoubleDispo = $chambreRepository->NbChambreDoubleDispo($hotel_id);
        $reservation = new Reservation();
        $client = $clientRepository->find(1);
        $voyage = $voyageRepository->find($voyage_id);

        $nbchambreSingleDispo = $chambreRepository->NbChambreSingleDispo(  $voyage->getHotel()->getId());

        $diff = date_diff(new \DateTime($date_debut), new \DateTime($date_fin));
        $getChambreSinglePrixPerHotel=$chambreRepository->getChambreSinglePrixPerHotel( $voyage->getHotel()->getId());



        $reservation->setToken("");
        $reservation->setConfirme("confirme");
        $reservation->setDateDebut(new \DateTime($date_debut));
        $reservation->setDateFin(new \DateTime($date_fin));
        $reservation->setNbAdulte($nbadultes);
        $reservation->setNbEnfants($nbenfants);
        $reservation->setHotel(null);
        $reservation->setClient($client);
        $reservation->setVoyage($voyage);
        $reservation->setTransport(null);

        $reservation->setPrix($getChambreSinglePrixPerHotel[0]['prix']*$diff->days+$voyage->getPrixPersonne()*$nb);

        $reservation->setType("reservation voyage");
        $reservation->setNbChambreSingleReserve($nbsingle);
        $reservation->setNbChambreDoubleReserve($nbdouble);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($reservation);
        $entityManager->flush();

       /* $chambreAaffecteRes = $chambreRepository->getChambresDoubleWithLimit($hotel_id, $nbchambre2);
        foreach ($chambreAaffecteRes as $res) {
            $room = $chambreRepository->find($res->getId());
            $room->setReservation($reservation);
            $room->setOccupe('occupe');
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($room);
            $entityManager->flush();
        }

        $chambreAaffecteRes = $chambreRepository->getChambresSingleWithLimit($hotel_id, $nbchambre);
        foreach ($chambreAaffecteRes as $res) {
            $room = $chambreRepository->find($res->getId());
            $room->setReservation($reservation);
            $room->setOccupe('occupe');
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($room);
            $entityManager->flush();
        }

*/

        $formatted = $serializer->normalize($reservation);


        $json = $serializer->serialize($reservation, 'json');

        return new JsonResponse($formatted);
    }



    /**
     * @Route("/wiw/resapi/hotel", name="AjoutReservationhotelapi")
     */
    public function AjoutReservationHotelapi(SerializerInterface $serializer,VoyageRepository $voyageRepository,ReservationRepository $reservationRepository, HotelRepository $hotelRepository, ClientRepository $clientRepository, Request $request, ChambreRepository $chambreRepository): Response
    {


        $date_debut = $request->query->get("date_debut");
        $date_fin = $request->query->get("date_fin");

        $nbadultes = $request->query->get("nbadultes");
        $nbenfants = $request->query->get("nbenfants");
        $nbsingle = $request->query->get("nbsingle");
        $nbdouble = $request->query->get("nbdouble");
        $hotel_id = $request->query->get("hotel_id");

        $nb=$nbadultes+$nbenfants;

        // $voyage = new Voyage();

        $nbchambreDoubleDispo = $chambreRepository->NbChambreDoubleDispo($hotel_id);
        $reservation = new Reservation();
        $client = $clientRepository->find(1);
        $hotel = $hotelRepository->find($hotel_id);

        $nbchambreSingleDispo = $chambreRepository->NbChambreSingleDispo(  $hotel_id);


        $diff = date_diff(new \DateTime($date_debut), new \DateTime($date_fin));



        $getChambreSinglePrixPerHotel=$chambreRepository->getChambreSinglePrixPerHotel( $hotel_id);

        $reservation->setToken("");
        $reservation->setConfirme("confirmé");
        $reservation->setDateDebut(new \DateTime($date_debut));
        $reservation->setDateFin(new \DateTime($date_fin));
        $reservation->setNbAdulte($nbadultes);
        $reservation->setNbEnfants($nbenfants);
        $reservation->setHotel($hotel);
        $reservation->setClient($client);
        $reservation->setVoyage(null);
        $reservation->setTransport(null);
        if ($diff->d == 0) {
            $reservation->setPrix(1 * (($nbchambreDoubleDispo['0']['0']->getPrix() * $nbdouble) + ($nbchambreSingleDispo['0']['0']->getPrix() * $nbsingle)));

        } else {
            $reservation->setPrix($diff->d * (($nbchambreDoubleDispo['0']['0']->getPrix() * $nbdouble) + ($nbchambreSingleDispo['0']['0']->getPrix() * $nbsingle)));

        }
        $reservation->setType("reservation hotel");
        $reservation->setNbChambreSingleReserve($nbsingle);
        $reservation->setNbChambreDoubleReserve($nbdouble);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($reservation);
        $entityManager->flush();

        /* $chambreAaffecteRes = $chambreRepository->getChambresDoubleWithLimit($hotel_id, $nbchambre2);
         foreach ($chambreAaffecteRes as $res) {
             $room = $chambreRepository->find($res->getId());
             $room->setReservation($reservation);
             $room->setOccupe('occupe');
             $entityManager = $this->getDoctrine()->getManager();
             $entityManager->persist($room);
             $entityManager->flush();
         }

         $chambreAaffecteRes = $chambreRepository->getChambresSingleWithLimit($hotel_id, $nbchambre);
         foreach ($chambreAaffecteRes as $res) {
             $room = $chambreRepository->find($res->getId());
             $room->setReservation($reservation);
             $room->setOccupe('occupe');
             $entityManager = $this->getDoctrine()->getManager();
             $entityManager->persist($room);
             $entityManager->flush();
         }

 */

        $formatted = $serializer->normalize($reservation);


        $json = $serializer->serialize($reservation, 'json');

        return new JsonResponse($formatted);
    }


    /**
     * @Route("/wiw/api/listewiwapi", name="listeres", methods={"GET"})
     */
    public function liste(ReservationRepository $reservationRepository,VoyageRepository $articlesRepo,SerializerInterface $serializer)
    {
        // On récupère la liste des articles
        $articles = $reservationRepository->apiFindAll();

        // On spécifie qu'on utilise l'encodeur JSON
        $encoders = [new JsonEncoder()];

        // On instancie le "normaliseur" pour convertir la collection en tableau
        $normalizers = [new ObjectNormalizer()];

        // On instancie le convertisseur
        $serializer = new \Symfony\Component\Serializer\Serializer($normalizers, $encoders);

        // On convertit en json
        $jsonContent = $serializer->serialize($articles, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        // On instancie la réponse
        $response = new Response($jsonContent);

        // On ajoute l'entête HTTP
        $response->headers->set('Content-Type', 'application/json');

        // On envoie la réponse
        return $response;

    }



    /**
     * @Route("/wiw/api/listewiwapifilter", name="erer", methods={"GET"})
     */
    public function liste5(Request $request,ReservationRepository $reservationRepository,VoyageRepository $articlesRepo,SerializerInterface $serializer)
    {   $id = $request->get("id");
        // On récupère la liste des articles
        $articles = $reservationRepository->apiFindAllfilter($id);

        // On spécifie qu'on utilise l'encodeur JSON
        $encoders = [new JsonEncoder()];

        // On instancie le "normaliseur" pour convertir la collection en tableau
        $normalizers = [new ObjectNormalizer()];

        // On instancie le convertisseur
        $serializer = new \Symfony\Component\Serializer\Serializer($normalizers, $encoders);

        // On convertit en json
        $jsonContent = $serializer->serialize($articles, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        // On instancie la réponse
        $response = new Response($jsonContent);

        // On ajoute l'entête HTTP
        $response->headers->set('Content-Type', 'application/json');

        // On envoie la réponse
        return $response;

    }
    /**
     * @Route("/wiw/api/detailresapi", name="listeresdetail", methods={"GET"})
     */
    //Detail Reclamation
    public function detailReclamationAction(Request $request,ReservationRepository $voyageRepository)
    {
        $id = $request->get("id");

        $em = $this->getDoctrine()->getManager();
        // $voyage = $this->getDoctrine()->getManager()->getRepository(Voyage::class)->find($id);
        $voyage =$voyageRepository->resdetail($id);

        $encoder = new JsonEncoder();
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });
        $serializer = new \Symfony\Component\Serializer\Serializer([$normalizer], [$encoder]);
        $formatted = $serializer->normalize($voyage);
        return new JsonResponse($formatted);
    }

    /**
     * @Route("/wiw/api/delres", name="delteresapi",methods={"DELETE"})
     */

    public function suppresapiee(Request $request) {
        $id = $request->get("id");

        $em = $this->getDoctrine()->getManager();
        $reclamation = $em->getRepository(Reservation::class)->find($id);
        if($reclamation!=null ) {
            $em->remove($reclamation);
            $em->flush();

            $serialize = new \Symfony\Component\Serializer\Serializer([new ObjectNormalizer()]);
            $formatted = $serialize->normalize("Reclamation a ete supprimee avec success.");
            return new JsonResponse($formatted);

        }
        return new JsonResponse("id reclamation invalide.");


    }

    /**
     * @Route("/wiw/api/stats", name="lststatsapi", methods={"GET"})
     */
    public function stats(ReservationRepository $reservationRepository,VoyageRepository $articlesRepo,SerializerInterface $serializer)
    {
        $reclamation = $this->getDoctrine()->getManager()->getRepository(Reservation::class)->apiHotelsstats();
        $serialize = new \Symfony\Component\Serializer\Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($reclamation);

        return new JsonResponse($formatted);

    }


}
