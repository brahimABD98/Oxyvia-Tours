<?php

namespace App\Controller;
use Acme\Bundle\AcmeBundle\DQL;
use App\Entity\Place;
use App\Entity\Voyage;
use App\Form\VoyageType;
use App\Repository\HotelRepository;
use App\Repository\ReservationRepository;
use App\Repository\TransportRepository;
use App\Repository\VoyageRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("dashboard/gestion/voyage")
 */
class VoyageController extends AbstractController
{
    /**
     * @Route("/", name="voyage_index")
     */
    public function index(VoyageRepository $voyageRepository,Request $request): Response
    {$limit = 10;
        $page = (int)$request->query->get("page", 1);
        $filterville=$request->get('ville');
        $filtersdb=$request->get('datedebut');
        $filtersdf=$request->get('datefin');

        $datedeb=$voyageRepository->dateDebutGroupedBy();
        $datefin=$voyageRepository->datefinGroupedBy();
        $villes=$voyageRepository->VilleGroupedBy();

        $voyages = $voyageRepository->getPaginatedvoyage($page, $limit,$filterville,$filtersdb,$filtersdf);

        $total=count($voyageRepository->getTotalVoy($filterville,$filtersdb,$filtersdf));




        if($request->get('ajax')){
            return new JsonResponse([
                'content' => $this->renderView('voyage/content.html.twig',
                    compact('voyages', 'total', 'limit', 'page','filterville'))
            ]);
        }





        return $this->render('voyage/index.html.twig', [
            'voyages' => $voyageRepository->findAll(),
            'datedeb'=>$datedeb,
            'datefin'=>$datefin,
            'villes'=>$villes,
            'page'=>$page,
            'limit'=>$limit,

            'total'=>$total
        ]);
    }





    /**
     * @Route("/new", name="voyage_new", methods={"GET","POST"})
     */
    public function new(Request $request,ParameterBagInterface $params,HotelRepository $hotelRepository,TransportRepository $transportRepository): Response
    {
        $voyage = new Voyage();
        $form = $this->createForm(VoyageType::class, $voyage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();


            $hotel=$hotelRepository->find($form->get('hotel')->getData());

            $place1 = $request->request->get("place1");
            $att1 = $request->request->get("att1");
            $long1 = $request->request->get("long1");

            $place2 = $request->request->get("place2");
            $att2 = $request->request->get("att2");
            $long2 = $request->request->get("long2");

            $place3 = $request->request->get("place3");
            $att3 = $request->request->get("att3");
            $long3 = $request->request->get("long3");

            $placeobj=new Place();
            $placeobj->setNom($place1);
            $placeobj->setAltitude($att1);
            $placeobj->setLongitude($long1);

            $placeobj2=new Place();
            $placeobj2->setNom($place2);
            $placeobj2->setAltitude($att2);
            $placeobj2->setLongitude($long2);

            $placeobj3=new Place();
            $placeobj3->setNom($place3);
            $placeobj3->setAltitude($att3);
            $placeobj3->setLongitude($long3);

            $file=$request->files->get('voyage')['image'];
            $uploads_directory=$params->get('upload_directory');
            $filename=md5(uniqid()). '.'.$file->guessExtension();
            $file->move(
                $uploads_directory,
                $filename
            );
            $voyage->setImage($filename);

            $voyage->setHotel($hotel);

            $arr=[$placeobj,
                $placeobj2,
                $placeobj3
            ];
            $entityManager->persist($voyage);

            foreach ($arr as $ar){
                $voyage->getPlace()->add($ar);
            }

            $transports=$form['transport']->getData();
            foreach ($transports as $ar2){

                $trans=$transportRepository->find($ar2->getId());

                $trans->setVoyage($voyage);
            }


            $placeobj->addVoyage($voyage);
            $placeobj2->addVoyage($voyage);
            $placeobj3->addVoyage($voyage);

            $entityManager->flush();


            return $this->redirectToRoute('voyage_index');
        }

        return $this->render('voyage/new.html.twig', [
            'voyage' => $voyage,
            'image'=>$voyage->getImage(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="voyage_show", methods={"GET"})
     */
    public function show(Voyage $voyage): Response
    {
        return $this->render('voyage/show.html.twig', [
            'voyage' => $voyage,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="voyage_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Voyage $voyage): Response
    {
        $form = $this->createForm(VoyageType::class, $voyage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('voyage_index');
        }

        return $this->render('voyage/edit.html.twig', [
            'voyage' => $voyage,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="voyage_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Voyage $voyage): Response
    {
        if ($this->isCsrfTokenValid('delete'.$voyage->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($voyage);
            $entityManager->flush();
        }

        return $this->redirectToRoute('voyage_index');
    }

    /**
     * @Route("/users/data/download", name="users_data_download")
     */
    public function usersDataDownload(VoyageRepository  $voyageRepository)
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
        $lst=$voyageRepository->findAll();

        // On génère le html
        $html = $this->renderView('voyage/voyageDatapdf.html.twig',[
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
 * @Route("/api/liste/voy", name="liste", methods={"GET"})
 */
public function liste(VoyageRepository $articlesRepo,SerializerInterface $serializer)
{
     // On récupère la liste des articles
     $articles = $articlesRepo->apiFindAll();

     // On spécifie qu'on utilise l'encodeur JSON
     $encoders = [new JsonEncoder()];
 
     // On instancie le "normaliseur" pour convertir la collection en tableau
     $normalizers = [new ObjectNormalizer()];
 
     // On instancie le convertisseur
     $serializer = new Serializer($normalizers, $encoders);
 
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
     * @Route("/api/ajout", name="ajouttt", methods={"POST"})
     */
    public function addArticle(HotelRepository $hotelRepository,Request $request)
    {

        $voyage=new Voyage();
        $description=$request->query->get('description');
        $nom=$request->query->get('nom');
$voyage->setNom($nom);
$voyage->setDescription($description);
$em=$this->getDoctrine()->getManager();
$em->persist($voyage);
$em->flush();
$serializer=new Serializer([new ObjectNormalizer()]);
$formated=$serializer->normalize($voyage);
return new JsonResponse($formated);

     /*   
      $jsonRecu=$request->getContent();
        $post=$serializer->deserialize($jsonRecu,Voyage::class,'json');

        $hotel=$hotelRepository->find("2");
        $post->setHotel($hotel);
        $em->persist($post);
        $em->flush();
        return $this->json($post,201,[],['groups'=>'post:read']);
        */
    }

    /**
     * @Route("/api/supprimer/{id}", name="supprime", methods={"DELETE"})
     */
    public function removeArticle(Voyage $article)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($article);
        $entityManager->flush();
        return new Response('ok');
    }

    /**
     * @return string
     * @throws \Exception
     */
    private function generateToken2()
    {
        return rtrim(strtr(base64_encode(random_bytes(5)), '+/', '-_'), '=');
    }


    /******************Ajouter voyage*****************************************/
    /**
     * @Route("/api/addvoyage", name="ajoutvoyageapi", methods={"POST"})
     */


    public function ajoutvoyageapi(SerializerInterface $serializer,Request $request,HotelRepository $hotelRepository)
    {
        $voyage = new Voyage();
        $nom = $request->query->get("nom");
        $ville = $request->query->get("ville");

        $description = $request->query->get("description");
        $date_debut = $request->query->get("date_debut");
        $date_fin = $request->query->get("date_fin");

        $prix_personne = $request->query->get("prix_personne");
        $nb_personne = $request->query->get("nb_personne");

        $hotel_id = $request->query->get("hotel_id");
        $hotel=$hotelRepository->find($hotel_id);

        $place = $request->query->get("place");
        $longtitude = $request->query->get("longtitude");
        $alt = $request->query->get("alt");

        $placeobj=new Place();
        $placeobj->setNom($place);
        $placeobj->setAltitude($alt);
        $placeobj->setLongitude($longtitude);

        $voyage->getPlace()->add($placeobj);
        $placeobj->addVoyage($voyage);



        $em = $this->getDoctrine()->getManager();



        $voyage->setNom($nom);
        $voyage->setVille($ville);
        $voyage->setDescription($description);
        $voyage->setDateDebut(new \DateTime($date_debut));
        $voyage->setDateFin(new \DateTime($date_fin));
        $voyage->setPrixPersonne($prix_personne);
        $voyage->setNbPersonne($nb_personne);
        $voyage->setImage("");
        $voyage->setHotel($hotel);


        $em->persist($voyage);
        $em->flush();
      //  $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($voyage);


        $json = $serializer->serialize($voyage, 'json', ['groups' => ['post:read']]);

        return new JsonResponse($formatted);

    }






    /**
     * @Route("/api/delvoyage", name="deletevoyageapi",methods={"DELETE"})
     */

    public function suppvoyageapiee(Request $request) {
        $id = $request->get("id");

        $em = $this->getDoctrine()->getManager();
        $reclamation = $em->getRepository(Voyage::class)->find($id);
        if($reclamation!=null ) {
            $em->remove($reclamation);
            $em->flush();

            $serialize = new Serializer([new ObjectNormalizer()]);
            $formatted = $serialize->normalize("Reclamation a ete supprimee avec success.");
            return new JsonResponse($formatted);

        }
        return new JsonResponse("id reclamation invalide.");


    }



    /**
     * @Route("/api/voyagefilter", name="voufilter", methods={"GET"})
     */
    public function liste5(Request $request,VoyageRepository $articlesRepo,SerializerInterface $serializer)
    {   $id = $request->get("id");
        // On récupère la liste des articles
        $articles = $articlesRepo->apiFindAllfilter($id);

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
     * @Route("/api/detailvoyage", name="detail_voyage",methods={"GET"})
     */
    //Detail Reclamation
    public function detailReclamationAction(Request $request,VoyageRepository $voyageRepository)
    {
        $id = $request->get("id");

        $em = $this->getDoctrine()->getManager();
       // $voyage = $this->getDoctrine()->getManager()->getRepository(Voyage::class)->find($id);
        $voyage =$voyageRepository->voyagedetail($id);

        $encoder = new JsonEncoder();
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });
        $serializer = new Serializer([$normalizer], [$encoder]);
        $formatted = $serializer->normalize($voyage);
        return new JsonResponse($formatted);
    }


    /**
     * @Route("/api/liste/hotels", name="lsthotels", methods={"GET"})
     */
    public function listehotels(HotelRepository $articlesRepo,SerializerInterface $serializer)
    {
        // On récupère la liste des articles
        $articles = $articlesRepo->apifindallHotels();

        // On spécifie qu'on utilise l'encodeur JSON
        $encoders = [new JsonEncoder()];

        // On instancie le "normaliseur" pour convertir la collection en tableau
        $normalizers = [new ObjectNormalizer()];

        // On instancie le convertisseur
        $serializer = new Serializer($normalizers, $encoders);

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

}
