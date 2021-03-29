<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Depense;
use App\Entity\Facture;
use App\Form\ContactType;
use App\Form\FactureType;
use App\Repository\FactureRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
     * @Route("/dashboard/showFacture", name="showFacture")
     */
    public function Affiche(Request $request,PaginatorInterface $paginator){
        $repo=$this->getDoctrine()->getRepository(Facture::class);
        $facture=$repo->findAll();
        /**
         * @var $paginator \Knp\Component\Pager\Paginator
         *
         */

        $pagination = $paginator->paginate(
            $facture,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 3)/*nbre d'éléments par page*/
        );
        return $this->render('facture/Affiche.html.twig',
            ['facture'=>$pagination]);
    }
    /**
     * @Route("/delete/{id}",name="delete")
     */

    function Delete($id, FactureRepository $repository)
    { $facture=$repository->find($id);
        $em=$this->getDoctrine()->getManager();
        $em->remove($facture);
        $em->flush();
        $this->addFlash('deleteFacture','facture deleted!');
        return $this->redirectToRoute('showFacture');

    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Route("Facture/Add/{res}",name="ajout")
     */
    function Add(Request $request,$res){
        $facture=new Facture();
        $form=$this->createForm(FactureType::class,$facture);
        //$form->add('Add',SubmitType::class);
        $form->handleRequest($request);
        /*$facture->setMontant(

                $depense->get('occupation')->getData()
            );*/
        if($form->isSubmitted() && $form->isValid()){
            $em=$this->getDoctrine()->getManager();
            $em->persist($facture);
            $em->flush();
            $this->addFlash('facture','Order completed!');
            $this->addFlash('facture','
             You ll receive a confirmation email at mail@example.com');

            return $this->redirectToRoute('show3');
        }
        return $this->render('facture/Add.html.twig',[
            'form'=>$form->createView(),
            'res'=>$res
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
            $this->addFlash('updateFacture','recently facture updated!');
            return $this->redirectToRoute('enabled');
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

    /**
     * @Route("Facture/Search",name="recherche")
     */
    function Recherche(FactureRepository $repository,Request $request)
    {    $data=$request->get('search');
        $facture=$repository->findBy(['identifiant'=>$data]);
        return $this->render('facture/Affiche.html.twig',
            ['facture'=>$facture]);

    }

    /**
     * @param FactureRepository $repository
     * @return Response
     * @Route("Facture/Trie",name="trie")
     */
   function OrderByMailSQL( FactureRepository $repository,Request $request,PaginatorInterface $paginator)
   { $facture=$repository->OrderById();
       /**
        * @var $paginator \Knp\Component\Pager\Paginator
        *
        */

       $pagination = $paginator->paginate(
           $facture,
           $request->query->getInt('page', 1),
           $request->query->getInt('limit', 3)/*nbre d'éléments par page*/
       );
   return $this->render('facture/Affiche.html.twig',
           ['facture'=>$pagination]);
   }

    /**
     * @param FactureRepository $repository
     * @param Request $request
     * @return Response
     * @Route("Facture/searchM",name="searchM")
     */
   function Search(FactureRepository $repository,Request $request,PaginatorInterface $paginator)
   { $identifiant=$request->get('search')   ;
     $id=$request->get('search');
     $montant=$request->get('search');
     $date_paiement=$request->get('search');
     $devise=$request->get('search');
     $moyen_paiement=$request->get('search');
     $mode_paiement=$request->get('search');
     $typeCB=$request->get('search');
     $Ncb=$request->get('search');
     $code_securite=$request->get('search');
     $date_expiration=$request->get('search');
     $location=$request->get('search');
     $pays=$request->get('search');
     $facture=$repository->SearchID($identifiant,$id,$montant,$date_paiement,$devise,$moyen_paiement,$mode_paiement,$typeCB,$Ncb,$code_securite,$date_expiration,$location,$pays);

       /**
        * @var $paginator \Knp\Component\Pager\Paginator
        *
        */

       $pagination = $paginator->paginate(
           $facture,
           $request->query->getInt('page', 1),
           $request->query->getInt('limit', 3)/*nbre d'éléments par page*/
       );
       return $this->render('facture/Affiche.html.twig',
           ['facture'=>$pagination]);}

    /**
     * @param FactureRepository $repository
     * @return Response
     * @Route("Facture/showDateSup",name="datesup")
     */

   function shows(FactureRepository $repository,Request $request,PaginatorInterface $paginator)
   { $facture=$repository->findEnabled();
       /**
        * @var $paginator \Knp\Component\Pager\Paginator
        *
        */

       $pagination = $paginator->paginate(
           $facture,
           $request->query->getInt('page', 1),
           $request->query->getInt('limit', 1)/*nbre d'éléments par page*/
       );
       return $this->render('facture/facturepaye.html.twig',
           ['facture'=>$pagination]);
   }
    /**
     * @param FactureRepository $repository
     * @return Response
     * @Route("Facture/showDateinf",name="dateinf")
     */

    function sho(FactureRepository $repository)
    { $facture=$repository->findEnabled2();

        return $this->render('facture/facturenompaye.html.twig',
            ['facture'=>$facture]);
    }

    /**
     * @param FactureRepository $repository
     * @return Response
     * @Route("Facture/enabled",name="enabled")
     */
    function ShowEnabled(FactureRepository $repository,PaginatorInterface $paginator,Request $request)
    { $facture=$repository->findEnabled();
        /**
         * @var $paginator \Knp\Component\Pager\Paginator
         *
         */

        $pagination = $paginator->paginate(
            $facture,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 3)/*nbre d'éléments par page*/
        );
        return $this->render('facture/Affiche.html.twig',
            ['facture'=>$pagination]);
    }


    /**
     * @param FactureRepository $repository
     * @return Response
     * @Route("Facture/enabled2",name="enabled2")
     */
    function ShowEnabled2(FactureRepository $repository,Request $request,PaginatorInterface $paginator)
    { $facture=$repository->findEnabled2();
        /**
         * @var $paginator \Knp\Component\Pager\Paginator
         *
         */

        $pagination = $paginator->paginate(
            $facture,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 3)/*nbre d'éléments par page*/
        );
        return $this->render('facture/Affiche.html.twig',
            ['facture'=>$pagination]);
    }






    /**
     * @return Response
     * @Route("Facture/map",name="map")
     */
    public function MapAction()
    {
        return $this->render('facture/map.html.twig');
    }


    /**
     * @Route("Facture/pdf/{id}",name="pdf_facture")
     */
    public function pdfActionn($id)
    {
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFront', 'Arial');
        $repo = $this->getDoctrine()->getRepository(Facture::class);
        $facture=$repo->find($id);
        $dompdf = new Dompdf ($pdfOptions);
        $html = $this->renderView('facture/pdf2.html.twig', array('f' => $facture));
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("mypdf.pdf", ["Attachment" => false]);


    }


    /**
     * @Route("Facture/pdf2/{id}",name="pdf_facture2")
     */
    public function pdfAction2($id)
    {
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFront', 'Arial');
        $repo = $this->getDoctrine()->getRepository(Facture::class);
        $facture = $repo->find($id);

        $dompdf = new Dompdf ($pdfOptions);
        $html = $this->renderView('facture/pdf4.html.twig', array('f' => $facture));
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("mypdf.pdf", ["Attachment" => false]);


    }

    /**
     * @param FactureRepository $repository
     * @param Request $request
     * @return Response
     * @Route("/SearchFacturePayee",name="SearchFacturePayee")
     */
    function SearchFacturePayee(FactureRepository $repository,Request $request)
    { $identifiant=$request->get('search')   ;
        $id=$request->get('search');
        $montant=$request->get('search');
        $date_paiement=$request->get('search');
        $devise=$request->get('search');
        $moyen_paiement=$request->get('search');
        $mode_paiement=$request->get('search');
        $typeCB=$request->get('search');
        $Ncb=$request->get('search');
        $code_securite=$request->get('search');
        $date_expiration=$request->get('search');
        $location=$request->get('search');
        $pays=$request->get('search');
        $facture=$repository->SearchID($identifiant,$id,$montant,$date_paiement,$devise,$moyen_paiement,$mode_paiement,$typeCB,$Ncb,$code_securite,$date_expiration,$location,$pays);


        return $this->render('facture/facturenompaye.html.twig',
            ['facture'=>$facture]);}

    /**
     * @param FactureRepository $repository
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     * @Route("/SearchFactureNonPayee",name="SearchFactureNonPayee")
     */

    function SearchFactureNonPayee(FactureRepository $repository,Request $request,PaginatorInterface $paginator)
    { $identifiant=$request->get('search')   ;
        $id=$request->get('search');
        $montant=$request->get('search');
        $date_paiement=$request->get('search');
        $devise=$request->get('search');
        $moyen_paiement=$request->get('search');
        $mode_paiement=$request->get('search');
        $typeCB=$request->get('search');
        $Ncb=$request->get('search');
        $code_securite=$request->get('search');
        $date_expiration=$request->get('search');
        $location=$request->get('search');
        $pays=$request->get('search');
        $facture=$repository->SearchID($identifiant,$id,$montant,$date_paiement,$devise,$moyen_paiement,$mode_paiement,$typeCB,$Ncb,$code_securite,$date_expiration,$location,$pays);

        /**
         * @var $paginator \Knp\Component\Pager\Paginator
         *
         */

        $pagination = $paginator->paginate(
            $facture,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 3)/*nbre d'éléments par page*/
        );
        return $this->render('facture/facturepaye.html.twig',
            ['facture'=>$pagination]);}

    /**
     * @param Request $request
     * @return Response
     * @Route("/contact",name="contact")
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
                      ->attach(\Swift_Attachment::fromPath('img/logos/mypdf.pdf'))

                      ->setBody($this->renderView(
                          'emails/contact.html.twig',
                          compact('contact')



                      ),
                      'text/html');
                  $mailer->send($message);
                  $this->addFlash('send','le message a bien été envoyé');
                  return $this->redirectToRoute('dateinf');

              }

             return $this->render('facture/mailing.html.twig',[
                 'contactForm'=>$form->createView()
             ]);


            }

    /**
     * @param Request $request
     * @return Response
     * @Route("/contact1",name="contact1")
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
                ->attach(\Swift_Attachment::fromPath('img/logos/mypdf (1).pdf'))

                ->setBody($this->renderView(
                    'emails/contact.html.twig',
                    compact('contact')



                ),
                    'text/html');
            $mailer->send($message);
            $this->addFlash('send','le message a bien été envoyé');
            return $this->redirectToRoute('datesup');

        }

        return $this->render('facture/mailing.html.twig',[
            'contactForm'=>$form->createView()
        ]);


    }

    /**
     * @Route("/dashboard/stats",name="stats")
     */
    public function stats(FactureRepository $repository)
    {
        $factures=$repository->findAll();
        $factureEtat=[];
        $factureColor=[];
        $factureCount=[];
        foreach ($factures as $facture){
            $factureEtat[] = $facture->getEnabled();
            $factureColor[]=$facture->getColor();
            $factureCount[]=count($factures);
        }
        $factures=$repository->countByDate();
        //dd($factures);
        $dates=[];
        $FactureByDateCount=[];
        foreach ($factures as $facture){
            $dates[]=$facture['dateFacture'];
            $FactureByDateCount[]=$facture['count'];
        }

     return $this->render('facture/stats.html.twig',
     ['factureEtat'=> json_encode($factureEtat),
         'factureColor'=>json_encode($factureColor),
         'factureCount'=>json_encode($factureCount),
         'dates'=>json_encode($dates),
         'FactureByDateCount'=>json_encode($FactureByDateCount)

         ]);

    }




}
