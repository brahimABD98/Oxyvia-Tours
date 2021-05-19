<?php

namespace App\Controller;

use App\Entity\Chambre;
use App\Entity\Client;
use App\Entity\Comment;
use App\Entity\Hotel;
use App\Entity\Transport;
use App\Form\ChambreType;
use App\Form\ClientType;
use App\Form\CommentType;
use App\Form\HotelType;
use App\Form\TransportType;
use App\Form\search;
use App\Repository\ChambreRepository;
use App\Repository\ClientRepository;
use App\Repository\CommentRepository;
use App\Repository\HotelRepository;
use App\Repository\TransportRepository;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

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
     * @Route("/dashboard/bookings", name="bookings", methods={"GET","POST"})
     */
    public function index1(TransportRepository $transportRepository,HotelRepository $hotelRepository,ChambreRepository $chambreRepository,PaginatorInterface $paginator,Request $request ): Response
    {

        $hotel = new Hotel();
        $searchForm = $this->createForm(\App\Form\SearchType::class,$hotel);
        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted()) {
            $name = $searchForm['name']->getData();
            $donnees = $hotelRepository->search($name);

            dd($donnees);
            return $this->redirectToRoute('search', array('name' => $name));
        }
        $donnees = $this->getDoctrine()->getRepository(Hotel::class)->findBy([],['id' => 'desc']);

        // Paginate the results of the query
        $hotels = $paginator->paginate(
        // Doctrine Query, not results
            $donnees,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            4
        );

        // Paginate the results of the query
        $donnees1=$chambreRepository->findAll();
        $cambres = $paginator->paginate(
        // Doctrine Query, not results
            $donnees1,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            4
        );
        $donnees2=$transportRepository->findAll();
        $trans = $paginator->paginate(
        // Doctrine Query, not results
            $donnees2,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            2
        );
        return $this->render('back/bookings.html.twig', [
            'hotels' => $hotels,
            'transports' => $trans,
            'chambres' =>$cambres,
            'searchForm' => $searchForm->createView()
        ]);


    }
    /**
     * @Route("/type/{type}", name="type", methods={"GET"})
     */

    public function Type(ChambreRepository $ChambreRepository,$type): Response
    {
        $Chambretype = $ChambreRepository->findBy(['idhotel' => $type]);
        return $this->render('front/type.html.twig', [
            'chambres' => $Chambretype,
        ]);
    }
    /**
     * @Route("/typeh/{typeh}", name="typeh", methods={"GET","POST"})
     */

    public function Typeh(HotelRepository $hotelRepository,$typeh,Request $request,ClientRepository $clientRepository,CommentRepository $commentRepository): Response
    {
        $Hoteltype = $hotelRepository->findBy(['id' => $typeh]);
        $comment = new Comment();
        $form = $this->createForm(CommentType::class,$comment);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();
            $val = $entityManager->getRepository(Hotel::class)->find($typeh);
            $value = $clientRepository->find($this->get('session')->get('id'));
            $comment->setCreatedAt(new \DateTime())
                ->setIdhotel($val)
                ->setIdclient($value);

            $entityManager->persist($comment);
            $entityManager->flush();
            return $this->redirectToRoute('typeh',['typeh'=>$typeh]);
        }
        $hotelcomment = $commentRepository->findBy(['idhotel'=>$typeh]);
        return $this->render('front/comment.html.twig', [
            'hotels' => $Hoteltype,
            'comments'=>$hotelcomment,
            'form'=>$form->createView(),
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
     * @Route("/deletehotel/{id}", name="deletehotel")
     */
    public function delete($id)
    {
        $em=$this->getDoctrine()->getManager();
        $h=$em->getRepository(Hotel::class)->find($id);
        $em->remove($h);
        $em->flush();
        return $this->redirectToRoute("bookings");
    }
    /**
     * @Route("/deletechambre/{id}", name="deletechambre")
     */
    public function delete2($id)
    {
        $em=$this->getDoctrine()->getManager();
        $h=$em->getRepository(Chambre::class)->find($id);
        $em->remove($h);
        $em->flush();
        return $this->redirectToRoute("bookings");
    }

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
    public function new1(Request $request): Response
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
     * @Route("/hotel_edit/{id}", name="hotel_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Hotel $hotel,HotelRepository $hotelRepository,$id): Response
    {
        $form = $this->createForm(HotelType::class, $hotel);
        $form->handleRequest($request);
        $hot =$hotelRepository->find($id);
        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form['image']->getData();
            $filename = md5(uniqid()).'.'.$uploadedFile->guessExtension();
            $uploadedFile->move($this->getParameter('upload_directory'),$filename);
            $hot->setImage($filename);
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
    public function edit1(Request $request, Transport $transport,$id,TransportRepository $transportRepository): Response
    {
        $form = $this->createForm(TransportType::class, $transport);
        $form->handleRequest($request);
        $tr =$transportRepository->find($id);
        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form['image']->getData();
            $filename = md5(uniqid()).'.'.$uploadedFile->guessExtension();
            $uploadedFile->move($this->getParameter('upload_directory'),$filename);
            $tr->setImage($filename);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('bookings');
        }

        return $this->render('transport/edit.html.twig', [
            'transport' => $transport,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/{id}/edit_chambre", name="chambre_edit", methods={"GET","POST"})
     */
    public function edit2(Request $request, Chambre $chambre,ChambreRepository $chambreRepository,$id): Response
    {
        $form = $this->createForm(ChambreType::class, $chambre);
        $form->handleRequest($request);
        $ch =$chambreRepository->find($id);
        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form['image']->getData();
            $filename = md5(uniqid()).'.'.$uploadedFile->guessExtension();
            $uploadedFile->move($this->getParameter('upload_directory'),$filename);
            $ch->setImage($filename);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('bookings');
        }

        return $this->render('chambre/edit.html.twig', [
            'chambre' => $chambre,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/list", name="list")
     */
    public function index12(HotelRepository $hotelRepository,ClientRepository $clientRepository): Response
    {
        $client= new Client();
        $val = $clientRepository->find(1);
        $session=new Session();
        $session->set('id',$val->getId());
        $session->set('nom',$val->getNom());
        return $this->render('front/hotels-list.html.twig', [
            'hotels' => $hotelRepository->findAll(),
        ]);
    }
    /**
     * @Route("/deletecom/{id}",name="deletecom",methods={"DELETE"})
     */
    public function deletecom(Request $request,Comment $comment)
    {
        if($this->isCsrfTokenValid('delete'.$comment->getId(),$request->request->get('_token')))
        {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($comment);
            $entityManager->flush();
        }
        return $this->redirectToRoute('list');
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
     * @Route("/chambre_new", name="chambre_new", methods={"GET","POST"})
     */
    public function new2(Request $request, HotelRepository $hotelRepository): Response
    {
        $chambre = new Chambre();
        $form = $this->createForm(ChambreType::class, $chambre);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form['image']->getData();
            $filename = md5(uniqid()).'.'.$uploadedFile->guessExtension();
            $uploadedFile->move($this->getParameter('upload_directory'),$filename);
            $chambre->setImage($filename);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($chambre);
            $entityManager->flush();

            return $this->redirectToRoute('bookings');
        }

        return $this->render('chambre/new.html.twig', [
            'hotels' => $hotelRepository->findAll(),
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/map/{id}", name="map")
     */
    public function map(HotelRepository $repository,$id): Response
    {

        $hotels= $repository->find($id);
        return $this->render('front/newMap.html.twig', [
            "hotels"=>$hotels

        ]);
    }

    /**
     * @Route("/listh", name="listh", methods={"GET"})
     */
    public function listh(HotelRepository $hotelRepository): Response
    {
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('back/listh.html.twig', [
            'hotels' => $hotelRepository->findAll(),
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (inline view)
        $dompdf->stream("mypdf.pdf", [
            "Attachment" => false
        ]);
    }
    /**
     * @Route("/listc", name="listc", methods={"GET"})
     */
    public function listc(ChambreRepository $ChambreRepository): Response
    {
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('back/listc.html.twig', [
            'chambres' => $ChambreRepository->findAll(),
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (inline view)
        $dompdf->stream("mypdf.pdf", [
            "Attachment" => false
        ]);
    }





    /**
     * @Route("/listt", name="listt", methods={"GET"})
     */
    public function listt(transportRepository $transportRepository): Response
    {
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('back/listt.html.twig', [
            'transports' => $transportRepository->findAll(),
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (inline view)
        $dompdf->stream("mypdf.pdf", [
            "Attachment" => false
        ]);
    }
    /**
     * @Route("/search/{name}", name="search", methods={"GET","POST"})
     */
    public function search(HotelRepository $hotelRepository,$name,Request $request,TransportRepository $transportRepository,ChambreRepository $chambreRepository): Response
    {
        $hotel = new Hotel();
        $searchForm = $this->createForm(\App\Form\SearchType::class,$hotel);
        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted()) {
            $nom = $searchForm['nom']->getData();
            $donnees = $hotelRepository->search($nom);
            return $this->redirectToRoute('search', array('nom' => $nom));
        }
        $hotel= $hotelRepository->search($name);
        return $this->render('back/bookings.html.twig', [
            'hotels' => $hotel,
            'transports' => $transportRepository->findAll(),
            'chambres' => $chambreRepository->findAll(),
            'searchForm' => $searchForm->createView()
        ]);

    }
    /**
     * @Route("/client_new", name="client_new", methods={"GET","POST"})
     */
    public function new3(Request $request): Response
    {
        $client = new Client();
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($client);
            $entityManager->flush();

            return $this->redirectToRoute('client_index');
        }

        return $this->render('client/new.html.twig', [
            'client' => $client,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/searchHotel", name="searchHotel")
     */
    public function searchHotel(Request $request,NormalizerInterface $Normalizer,HotelRepository $repository)
    {

        $requestString=$request->get('searchValue');
        $hotels = $repository->search($requestString);
        $jsonContent = $Normalizer->normalize($hotels, 'json',['groups'=>'hotel:read']);
        $retour=json_encode($jsonContent);

        return new Response($retour);

    }
    //// les methodes de recomobiles
    /**
     *


    @Route("/hotelmobileindex/affichage", name="hotel_mobileindex")
    */
    public function indexmobilehotel(NormalizerInterface $Normalizer ): Response
    {        $recos = $this->getDoctrine()
        ->getRepository(Hotel::class)
        ->findAll();
        $jsoncontent=$Normalizer->normalize($recos,'json',['groups'=>'hotel:read']);
        return new Response (json_encode($jsoncontent));




    }

    /**
     *


    @Route("/chambremobileindex/affichage{id}", name="chambre_mobileindex")
     */
    public function indexmobilechambre(NormalizerInterface $Normalizer,$id ): Response
    {        $recos = $this->getDoctrine()
        ->getRepository(Chambre::class)
        ->findBy(['idhotel'=>$id]);
        $jsoncontent=$Normalizer->normalize($recos,'json',['groups'=>'post:read']);
        return new Response (json_encode($jsoncontent));




    }






    }

