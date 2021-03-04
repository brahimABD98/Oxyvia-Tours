<?php

namespace App\Controller;

use App\Entity\Facture;
use App\Form\FactureType;
use App\Repository\FactureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FactureController extends AbstractController
{
    /**
     * @Route("/facture", name="facture")
     */
    public function index(): Response
    {
        return $this->render('facture/index.html.twig', [
            'controller_name' => 'FactureController',
        ]);
    }
    /**
     * @Route("/showFacture", name="showFacture")
     */
    public function Affiche(){
        $repo=$this->getDoctrine()->getRepository(Facture::class);
        $facture=$repo->findAll();
        return $this->render('facture/Affiche.html.twig',
            ['facture'=>$facture]);




    }
    /**
     * @Route("/delete/{id}",name="delete")
     */

    function Delete($id, FactureRepository $repository)
    { $facture=$repository->find($id);
        $em=$this->getDoctrine()->getManager();
        $em->remove($facture);
        $em->flush();
        return $this->redirectToRoute('showFacture');

    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Route("Facture/Add",name="ajout")
     */
    function Add(Request $request){
        $facture=new Facture();
        $form=$this->createForm(FactureType::class,$facture);
        //$form->add('Add',SubmitType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em=$this->getDoctrine()->getManager();
            $em->persist($facture);
            $em->flush();

            return $this->redirectToRoute('show3');
        }
        return $this->render('facture/Add.html.twig',[
            'form'=>$form->createView()
        ]);




    }
    /**
     * @Route("Facture/update/{id}",name="update")
     */
    public function update(FactureRepository $repository,$id,Request $request)
    { $facture=$repository->find($id);
        $form=$this->createForm(FactureType::class,$facture);
// $form->add('update',SubmitType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em=$this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('showFacture');
        }
        return $this->render('facture/Update.html.twig',[
            'form'=>$form->createView()
        ]);}


    /**
     * @return Response
     * @Route("/show3", name="show3")
     */
    function showw()
    {
        return $this->render('facture/cart3.html.twig');

    }





}
