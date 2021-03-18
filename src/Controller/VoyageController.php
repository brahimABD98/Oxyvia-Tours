<?php

namespace App\Controller;
use Acme\Bundle\AcmeBundle\DQL;
use App\Entity\Voyage;
use App\Form\VoyageType;
use App\Repository\VoyageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    {$limit = 4;
        $page = (int)$request->query->get("page", 1);

        $voyage=new Voyage();
        $form = $this->createFormBuilder($voyage, ['attr' => ['id' => 'filters']])

        ->add(
            'ville',
            EntityType::class,
            [
                'class' => Voyage::class,

                'expanded' => false,
                'multiple' => false,
                 'placeholder'=>"selectionez la ville du voyage",

                'query_builder' => function (VoyageRepository $er) {
                    return $er->createQueryBuilder('u')
                            ->groupBy('u.ville') ;

                },
                'choice_label' => 'ville',
                'choice_value' => 'ville',
            ]
        )
            ->add(
                'date_debut',
                EntityType::class,
                [
                    'class' => Voyage::class,
                    'choice_label' => 'getJourDebutFormat',
                    'expanded' => false,
                    'multiple' => false,
                    'placeholder'=>"selectionez date fin du voyage",

                    'query_builder' => function (VoyageRepository $er) {
                        return $er->createQueryBuilder('u')
                            ->select('DATE_FORMAT(u.date_debut, "%d %m %Y") as dateAsMonth')
                            ->groupBy('as dateAsMonth')
                        ->getQuery()
                            ->getResult();
                    },
                    'choice_label' => 'date_debut',
                    'choice_value' => 'date_debut',

                ]
            )
            ->add(
                'date_fin',
                EntityType::class,
                [
                    'class' => Voyage::class,
                    'choice_label' => 'getJourfinFormat',
                    'expanded' => false,
                    'multiple' => false,
                    'placeholder'=>"selectionez date fin du voyage",


                ]
            )
            ->getForm();


        return $this->render('voyage/index.html.twig', [
            'voyages' => $voyageRepository->findAll(),
            'form' => $form->createView(),
            'page'=>$page
        ]);
    }





    /**
     * @Route("/new", name="voyage_new", methods={"GET","POST"})
     */
    public function new(Request $request,ParameterBagInterface $params): Response
    {
        $voyage = new Voyage();
        $form = $this->createForm(VoyageType::class, $voyage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file=$request->files->get('voyage')['image'];
           $uploads_directory=$params->get('uploads_directory');
           $filename=md5(uniqid()). '.'.$file->guessExtension();
           $file->move(
               $uploads_directory,
               $filename
           );
          $voyage->setImage($filename);

            $entityManager = $this->getDoctrine()->getManager();
           $entityManager->persist($voyage);
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
