<?php

namespace App\Controller;

use App\Entity\Depense;
use App\Form\DepenseType;
use App\Repository\DepenseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Cocur\Slugify\Slugify;
class DepenseController extends AbstractController
{
    /**
     * @Route("/depense", name="depense")
     */
    public function index(): Response
    {
        return $this->render('depense/index.html.twig', [
            'controller_name' => 'DepenseController',
        ]);
    }



    /**
     * @Route("/showDepense", name="showDepense")
     */
    public function Affiche()
    {
        $repo = $this->getDoctrine()->getRepository(Depense::class);
        $depense = $repo->findAll();
        return $this->render('depense/Affiche.html.twig',
            ['depense' => $depense]);


    }

    /**
     * @Route("/deletee/{id}",name="deletee")
     */
    public function deletee($id, DepenseRepository $repository)
    {
        $depense = $repository->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($depense);
        $em->flush();
        return $this->redirectToRoute('showDepense');
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Route("Depense/Add",name="add")
     */
    function Add(Request $request){
        $depense=new Depense();
        $form=$this->createForm(DepenseType::class,$depense);
        //$form->add('Add',SubmitType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            /** @var UploadedFile $file */
            $file = $depense->getPicture();
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();
            try{
                $file->move(
                    $this->getParameter('images_directory'),$fileName
                );
            }catch(FileException $e){}
            $depense->setPicture($fileName);

            $em=$this->getDoctrine()->getManager();
            $em->persist($depense);
            $em->flush();

            return $this->redirectToRoute('showDepense');
        }
        return $this->render('depense/Add.html.twig',[
            'form'=>$form->createView()
        ]);




    }






    /**
     * @Route("Depense/update/{id}",name="updatee")
     */
    public function update(DepenseRepository $repository, $id, Request $request)
    {
        $depense = $repository->find($id);
        $form = $this->createForm(DepenseType::class, $depense);
        //$form->add('update', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            //$file = $depense->getPicture();
            $file = $form->get('picture')->getData();
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move(
                $this->getParameter('images_directory'), $fileName
            );
            $depense->setPicture($fileName);

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('showDepense');
        }
        return $this->render('Depense/Update.html.twig', [
            'form' => $form->createView()
        ]);
    }





















}
