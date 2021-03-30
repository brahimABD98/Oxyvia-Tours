<?php

namespace App\Controller;
use Acme\Bundle\AcmeBundle\DQL;
use App\Entity\Place;
use App\Entity\Voyage;
use App\Form\VoyageType;
use App\Repository\HotelRepository;
use App\Repository\TransportRepository;
use App\Repository\VoyageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

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
}
