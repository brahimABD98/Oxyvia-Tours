<?php

namespace App\Controller;

use App\Entity\EvenementsTable;
use App\Form\EvenementsTableType;
use App\Repository\EvenementsRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class EvenementsTableController extends AbstractController
{
    /**
     * @Route("/", name="evenements_table_index", methods={"GET"})
     */
    public function index(EvenementsRepository $evenementsRepository,PaginatorInterface $paginator,Request $request): Response
    {
        $event=$evenementsRepository->findAll();
        $events = $paginator->paginate(
        // Doctrine Query, not results
            $event,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            2
        );
        return $this->render('evenements_table/index.html.twig', [
            'evenements_tables' => $events,
        ]);
    }

    /**
     * @Route("/new", name="evenements_table_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $evenementsTable = new EvenementsTable();
        $form = $this->createForm(EvenementsTableType::class, $evenementsTable);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($evenementsTable);
            $entityManager->flush();

            return $this->redirectToRoute('evenements_table_index');
        }

        return $this->render('evenements_table/newEvent.html.twig', [
            'evenements_table' => $evenementsTable,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/", name="evenements_table_show", methods={"GET"})
     */
    public function show(EvenementsTable $evenementsTable): Response
    {
        return $this->render('evenements_table/show.html.twig', [
            'evenements_table' => $evenementsTable,
        ]);
    }

    /**
     * @Route("/editEvenements/{id}", name="editEvenement")
     */
    public function editEvenement(Request $request,$id){
        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository(EvenementsTable::class)->find($id);
        $form = $this->createForm(EvenementsTableType::class, $event);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();

            $em->flush();
            return $this->redirectToRoute('evenements_table_show');
        }
        return $this->render('evenements_table/edit.html.twig', [
            "f" => $form->createView(),
        ]);


    }

    /**
     * @Route("/EventDelete/{id}", name="evenements_table_delete")
     */
    public function delete($id): Response
    {
        $em=$this->getDoctrine()->getManager();
        $event=$em->getRepository(EvenementsTable::class)->find($id);
        $em->remove($event);
        $em->flush();
        return $this->redirectToRoute("evenements_table_show");
        }

    /**
     * @Route("/AfficherEvent", name="AfficherEvent")
     */
    public function afficherEvent(){
        $events=$this->getDoctrine()->getRepository(EvenementsTable::class)->findAll();
        return $this->render("evenements/index.html.twig",['events'=>$events]);
    }

    /**
     * @Route("/searchEvent", name="searchEvent")
     */
    public function searchFormation(Request $request,NormalizerInterface $Normalizer,EvenementsRepository $repository)
    {

        $requestString=$request->get('searchValue');
        $events = $repository->search($requestString);
        $jsonContent = $Normalizer->normalize($events, 'json',['groups'=>'event:read']);
        $retour=json_encode($jsonContent);

        return new Response($retour);

    }


}
