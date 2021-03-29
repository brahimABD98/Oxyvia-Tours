<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Depense;
use App\Entity\Facture;
use App\Form\ContactType;
use App\Form\DepenseType;
use App\Repository\DepenseRepository;
use Dompdf\Adapter\PDFLib;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Cocur\Slugify\Slugify;
use Knp\Component\Pager\PaginatorInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

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
    public function Affiche (Request $request,PaginatorInterface $paginator)
    {
        $repo = $this->getDoctrine()->getRepository(Depense::class);
        $depense = $repo->findAll();
        /**
         * @var $paginator \Knp\Component\Pager\Paginator
         *
         */

        $pagination = $paginator->paginate(
            $depense,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 3)/*nbre d'éléments par page*/
        );

        return $this->render('depense/Affiche.html.twig',
            ['depense' => $pagination]);


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
        $this->addFlash('delete','Depense deleted !');

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
            $this->addFlash('depense','NEW DEPENSE ADDED!');

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
            $this->addFlash('update','Depense updated !');
            return $this->redirectToRoute('enabledd');
        }
        return $this->render('Depense/Update.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param DepenseRepository $repository
     * @return Response
     * @Route("Depense/Trie",name="triee")
     */
    function OrderByMailSQL( DepenseRepository $repository,Request $request,PaginatorInterface $paginator)
    { $depense=$repository->OrderById();
        /**
         * @var $paginator \Knp\Component\Pager\Paginator
         *
         */

        $pagination = $paginator->paginate(
            $depense,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 3)/*nbre d'éléments par page*/
        );
        return $this->render('depense/Affiche.html.twig',
            ['depense'=>$pagination]);
    }

    /**
     * @param FactureRepository $repository
     * @return Response
     * @Route("Depense/enabled",name="enabledd")
     */
    function ShowEnabled(DepenseRepository $repository,PaginatorInterface $paginator,Request $request)
    { $depense=$repository->findEnabled();
        /**
         * @var $paginator \Knp\Component\Pager\Paginator
         *
         */

        $pagination = $paginator->paginate(
            $depense,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 3)/*nbre d'éléments par page*/
        );
        return $this->render('depense/Affiche.html.twig',
            ['depense'=>$pagination]);
    }


    /**
     * @param DepenseRepository $repository
     * @return Response
     * @Route("Depense/enabled2",name="enabledd2")
     */
    function ShowEnabled2(DepenseRepository $repository,Request $request,PaginatorInterface $paginator)
    { $depense=$repository->findEnabled2();
        /**
         * @var $paginator \Knp\Component\Pager\Paginator
         *
         */

        $pagination = $paginator->paginate(
            $depense,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 3)/*nbre d'éléments par page*/
        );
        return $this->render('depense/Affiche.html.twig',
            ['depense'=>$pagination]);
    }


    /**
     * @param DepenseRepository $repository
     * @return Response
     * @Route("Depense/showDateSup",name="datesupp")
     */

    function shows(DepenseRepository $repository,Request $request,PaginatorInterface $paginator)
    { $depense=$repository->findEnabled();

        $repo = $this->getDoctrine()->getRepository(Depense::class);
        $depense = $repo->findAll();
        /**
         * @var $paginator \Knp\Component\Pager\Paginator
         *
         */

        $pagination = $paginator->paginate(
            $depense,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 1)/*nbre d'éléments par page*/
        );
        return $this->render('depense/dpensepaye.html.twig',
            ['depense'=>$pagination]);
    }





    /**
     * @param DepenseRepository $repository
     * @return Response
     * @Route("Depense/showDateinf",name="dateinff")
     */

    function sho(DepenseRepository $repository)
    { $depense=$repository->findEnabled2();


        return $this->render('depense/depensenompaye.html.twig',
            ['depense'=>$depense]);
    }

    /**
     * @Route("Depense/pdf/{id}",name="pdf")
     */
    public function pdfAction($id)
    {
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFront', 'Arial');
        $pdfOptions->set('IsRemoteEnabled',true);
        $pdfOptions->set('isHtml5ParserEnabled',true);
       // $pdfOptions->setTempDir('temp');
        // $pdfOptions->set('chroot', realpath(base_path()));


        $repo = $this->getDoctrine()->getRepository(Depense::class);
        $depense = $repo->find($id);
        $html = $this->renderView('depense/pdf1.html.twig', array('d' => $depense));
        $dompdf = new Dompdf($pdfOptions);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');

        $dompdf->render();
        //$dompdf->setBasePath($_SERVER['DOCUMENT_ROOT']);
        $dompdf->stream("mypdf.pdf", ["Attachment" => false]);


        //return Dompdf::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadView('reports.invoiceSell')->stream();



    }
    /**
     * @Route("Depense/pdf3/{id}",name="pdf3")
     */
    public function pdfAction3($id)
    {
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFront','Arial');
        $pdfOptions->setIsRemoteEnabled(true);
        $repo = $this->getDoctrine()->getRepository(Depense::class);
        $depense = $repo->find($id);
        $dompdf = new Dompdf ($pdfOptions);
        $html = $this->renderView('depense/pdf3.html.twig', array('d' => $depense));
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("mypdf.pdf", ["Attachment" => false]);


    }





    /**
     * @param DepenseRepository $repository
     * @param Request $request
     * @return Response
     * @Route("Depense/searchMultiple",name="rechercheMultiple")
     */

    function Search(DepenseRepository $repository,Request $request,PaginatorInterface $paginator)
    { $prenom=$request->get('recherche') ;
      $nom=$request->get('recherche') ;
      $id=$request->get('recherche') ;
      $occupation=$request->get('recherche') ;
      $id_personnel=$request->get('recherche') ;
      $salaire=$request->get('recherche') ;
      $horaire_reguliere=$request->get('recherche');
      $horaire_sup=$request->get('recherche');
      $exempte=$request->get('recherche');
      $date_depense=$request->get('recherche');






        $depense=$repository->SearchID($prenom,$nom,$id,$occupation,$id_personnel,$salaire,$horaire_reguliere,$horaire_sup,$exempte,$date_depense);
        /**
         * @var $paginator \Knp\Component\Pager\Paginator
         *
         */

        $pagination = $paginator->paginate(
            $depense,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 3)/*nbre d'éléments par page*/
        );
        return $this->render('depense/Affiche.html.twig',
            ['depense'=>$pagination]);}

            /**
             * @Route("/searchDepensex",name="searchDepensex")
             */
            public function searchDepensex(Request $request,NormalizerInterface $Normalizer)
            { $repository = $this->getDoctrine()->getRepository(Depense::class);
              $requestString=$request->get('searchValue');
              $depense=$repository->findStudentById($requestString);
              $jsonContent = $Normalizer->normalize($depense,'json',['groups'=>'depense:read']);
              $retour=json_encode($jsonContent);
              return new Response($retour);

            }

    /**
     * @param DepenseRepository $repository
     * @param Request $request
     * @return Response
     * @Route("/Search_Listpayee",name="Search_Listpayee")
     */
    function Search_Listpayee(DepenseRepository $repository,Request $request)
    { $prenom=$request->get('recherche') ;
        $nom=$request->get('recherche') ;
        $id=$request->get('recherche') ;
        $occupation=$request->get('recherche') ;
        $id_personnel=$request->get('recherche') ;
        $salaire=$request->get('recherche') ;
        $horaire_reguliere=$request->get('recherche');
        $horaire_sup=$request->get('recherche');
        $exempte=$request->get('recherche');
        $date_depense=$request->get('recherche');






        $depense=$repository->SearchID($prenom,$nom,$id,$occupation,$id_personnel,$salaire,$horaire_reguliere,$horaire_sup,$exempte,$date_depense);



        return $this->render('depense/depensenompaye.html.twig',
            ['depense'=>$depense]);}

    /**
     * @param DepenseRepository $repository
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     * @Route("/Search_ListeNonPayee",name="Search_ListeNonPayee")
     */
    function Search_ListeNonPayee(DepenseRepository $repository,Request $request,PaginatorInterface $paginator)
    { $prenom=$request->get('recherche') ;
        $nom=$request->get('recherche') ;
        $id=$request->get('recherche') ;
        $occupation=$request->get('recherche') ;
        $id_personnel=$request->get('recherche') ;
        $salaire=$request->get('recherche') ;
        $horaire_reguliere=$request->get('recherche');
        $horaire_sup=$request->get('recherche');
        $exempte=$request->get('recherche');
        $date_depense=$request->get('recherche');




        $depense=$repository->SearchID($prenom,$nom,$id,$occupation,$id_personnel,$salaire,$horaire_reguliere,$horaire_sup,$exempte,$date_depense);
        /**
         * @var $paginator \Knp\Component\Pager\Paginator
         *
         */

        $pagination = $paginator->paginate(
            $depense,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 3)/*nbre d'éléments par page*/
        );
        return $this->render('depense/dpensepaye.html.twig',
            ['depense'=>$pagination]);}






/**
 * @param Request $request
 * @return Response
 * @Route("/contact2",name="contact2")
 */

public function sendMail(Request $request, \Swift_Mailer $mailer)
{ $contact=new Contact();
    $form=$this->createForm(ContactType::class,$contact);
    $form->handleRequest($request);
    if($form->isSubmitted() && $form->isValid())
    { $contact1= $form->getData();
        /** @var UploadedFile $file */
        //$file = $contact->getFichier();
        $file = $form->get('fichier')->getData();
        $fileName = md5(uniqid()) . '.' . $file->guessExtension();
        try{
            $file->move(
                $this->getParameter('images_directory'),$fileName
            );
        }catch(FileException $e){}
        $contact->setFichier($fileName);
        $em=$this->getDoctrine()->getManager();
        $em->persist($contact);
        $em->flush();
        //dd($contact);
        $message = (new \Swift_Message('Paiement Notification'))

            ->setFrom('eyaallahthebti99@gmail.com')
            ->setTo('eyaallahthebti99@gmail.com')
            //->setTo($contact['email'])
            // ->attach(\Swift_Attachment::fromPath($contact1['fichier']))
            ->attach(\Swift_Attachment::fromPath('img/logos/mypdf (2).pdf'))

            ->setBody($this->renderView(
                'emails/contact.html.twig',
                compact('contact')



            ),
                'text/html');
        $mailer->send($message);
        $this->addFlash('send','le message a bien été envoyé');
        return $this->redirectToRoute('dateinff');

    }

    return $this->render('depense/mailing.html.twig',[
        'contactForm'=>$form->createView()
    ]);


}
    /**
     * @param Request $request
     * @return Response
     * @Route("/contact3",name="contact3")
     */

    public function sendMail1(Request $request, \Swift_Mailer $mailer)
    { $contact=new Contact();
        $form=$this->createForm(ContactType::class,$contact);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        { $contact1= $form->getData();
            /** @var UploadedFile $file */
            //$file = $contact->getFichier();
            $file = $form->get('fichier')->getData();
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();
            try{
                $file->move(
                    $this->getParameter('images_directory'),$fileName
                );
            }catch(FileException $e){}
            $contact->setFichier($fileName);
            $em=$this->getDoctrine()->getManager();
            $em->persist($contact);
            $em->flush();
            //dd($contact);
            $message = (new \Swift_Message('Paiement Notification'))

                ->setFrom('eyaallahthebti99@gmail.com')
                ->setTo('eyaallahthebti99@gmail.com')
                //->setTo($contact['email'])
                // ->attach(\Swift_Attachment::fromPath($contact1['fichier']))
                ->attach(\Swift_Attachment::fromPath('img/logos/mypdf (3).pdf'))

                ->setBody($this->renderView(
                    'emails/contact.html.twig',
                    compact('contact')



                ),
                    'text/html');
            $mailer->send($message);
            $this->addFlash('send','le message a bien été envoyé');
            return $this->redirectToRoute('datesupp');

        }

        return $this->render('depense/mailing.html.twig',[
            'contactForm'=>$form->createView()
        ]);


    }

    /**
     * @Route("/stats1",name="stats1")
     */
    public function stats(DepenseRepository $repository)
    {  $depenses=$repository->findAll();
        $HoraireReguliere=[];
        $depenseColor=[];
        $depenseCount=[];
        foreach ($depenses as $depense){
            $HoraireReguliere[] = $depense->getHoraireReguliere();
            $depenseColor[]=$depense->getColor();
            $depenseSup[]=$depense->getHoraireSup();
            $depenseCount[]=count($depenses);
        }

        $depenses=$repository->countByOccupation();
        //dd($factures);
        $occupations=[];
        $DepByOccupationCount=[];
        foreach ($depenses as $depense){
            $occupations[]=$depense['occupations'];
            $DepByOccupationCount[]=$depense['count'];
        }

        return $this->render('depense/stats2.html.twig',
            [
                'occupations'=>json_encode($occupations),
                'DepByOccupationCount'=>json_encode($DepByOccupationCount),
                'HoraireReguliere'=>json_encode($HoraireReguliere),
                'depenseColor'=>json_encode($depenseColor),
                'depenseCount'=>json_encode($depenseCount),
                'depenseSup'=>json_encode($depenseSup)

            ]);

    }







}
