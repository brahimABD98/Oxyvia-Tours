<?php

namespace App\Controller;

use App\Entity\OffresTable;
use App\Form\OffresTableType;
use App\Repository\EvenementsRepository;
use App\Repository\OffresRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @Route("/offres/table")
 */
class OffresTableController extends AbstractController
{
    /**
     * @Route("/", name="offres_table_index", methods={"GET"})
     */
    public function index(OffresRepository $offresRepository,Request $request,PaginatorInterface $paginator): Response
    {
        $offre=$offresRepository->findAll();
        $offres = $paginator->paginate(
        // Doctrine Query, not results
            $offre,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            2
        );
        return $this->render('offres_table/index.html.twig', [
            'offres_tables' => $offres,
        ]);
    }

    /**
     * @Route("/new", name="offres_table_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $offresTable = new OffresTable();
        $form = $this->createForm(OffresTableType::class, $offresTable);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($offresTable);
            $entityManager->flush();

            return $this->redirectToRoute('offres_table_index');
        }

        return $this->render('offres_table/new.html.twig', [
            'offres_table' => $offresTable,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/offres_table_show/{id}", name="offres_table_show", methods={"GET"})
     */
    public function show(OffresTable $offresTable): Response
    {
        return $this->render('offres_table/show.html.twig', [
            'offres_table' => $offresTable,
        ]);
    }

    /**
     * @Route("/offres_table_edit/{id}/edit", name="offres_table_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, OffresTable $offresTable): Response
    {
        $form = $this->createForm(OffresTableType::class, $offresTable);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('offres_table_index');
        }

        return $this->render('offres_table/edit.html.twig', [
            'offres_table' => $offresTable,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="offres_table_delete", methods={"DELETE"})
     */
    public function delete(Request $request, OffresTable $offresTable): Response
    {
        if ($this->isCsrfTokenValid('delete' . $offresTable->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($offresTable);
            $entityManager->flush();
        }

        return $this->redirectToRoute('offres_table_index');
    }



}
