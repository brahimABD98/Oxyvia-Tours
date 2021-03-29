<?php

namespace App\Controller;

use App\Repository\ChambreRepository;
use App\Repository\VoyageRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(VoyageRepository $voyageRepository, MailerInterface $mailer,ChambreRepository $chambreRepository): Response
    {

        $authors = $chambreRepository
            ->showChambreExpire();

        if (count($authors)){
            $email = (new TemplatedEmail())
                ->from('saieftaher1@gmail.com')
                ->to('saieftaher1@gmail.com')
                ->subject('Liste des Chambres vides!')
                ->htmlTemplate('emailToAdmin/email.html.twig')
                ->context([
                    'chambreExpire' => $authors,

                ]);
        $mailer->send($email);
            foreach ($authors as $res){
                $entityManager = $this->getDoctrine()->getManager();
                $chambre=$chambreRepository->find($res->getID());
                $chambre->setOccupe('non occupe');
                $chambre->setReservation(null);
                $entityManager->persist($res);
                $entityManager->flush();
            }
    }


        $arrvoy=$voyageRepository->Voyagelist();

        return $this->render('home/index.html.twig', [
        'arrvoy'=>$arrvoy
        ]);
    }
    /**
     * @Route("/showAdventures", name="showAdventures")
     */
    public function adventures(){


        return $this->render('home/sweet.html');
    }
    /**
     * @Route("/sh1", name="sh1")
     */
    public function sh1(){


        return $this->render('home/index-2.html.twig');
    }
    /**
     * @Route("/sh2", name="sh2")
     */
    public function sh2(){


        return $this->render('home/index33.html.twig');
    }

    /**
     * @Route("/sh3", name="sh3")
     */
    public function sh3(){


        return $this->render('home/index-4.html.twig');
    }

    /**
     * @Route("/sh4", name="sh4")
     */
    public function sh4(){


        return $this->render('home/index-5.html.twig');
    }
    /**
     * @Route("/sh5", name="sh5")
     */
    public function sh5(){


        return $this->render('home/index-6.html.twig');
    }
    /**
     * @Route("/sh6", name="sh6")
     */
    public function sh6(){


        return $this->render('home/index-7.html.twig');
    }

    /**
     * @Route("/sh7", name="sh7")
     */
    public function sh7(){


        return $this->render('home/invoice.html.twig');
    }

    /**
     * @Route("/sh8", name="sh8")
     */
    public function sh8(){


        return $this->render('home/login.html.twig');
    }
    /**
     * @Route("/sh9", name="sh9")
     */
    public function sh9(){


        return $this->render('home/media-gallery.html.twig');
    }
    /**
     * @Route("/sh10", name="sh10")
     */
    public function sh10(){


        return $this->render('home/menu-options.html.twig');
    }


    /**
     * @Route("/sh11", name="sh11")
     */
    public function sh11(){


        return $this->render('home/pricing-tables.html.twig');
    }
    /**
     * @Route("/sh12", name="sh12")
     */
    public function sh12(){


        return $this->render('home/register.html.twig');
    }

    /**
     * @Route("/sh13", name="sh13")
     */
    public function sh13(){


        return $this->render('home/restaurant-detail.html.twig');
    }
    /**
     * @Route("/sh14", name="sh14")
     */
    public function sh14(){


        return $this->render('home/restaurant-grid.html.twig');
    }
    /**
     * @Route("/sh15", name="sh15")
     */
    public function sh15(){


        return $this->render('home/restaurant-grid-isotope.html.twig');
    }
    /**
     * @Route("/sh16", name="sh16")
     */
    public function sh16(){


        return $this->render('home/restaurant-grid-sidebar-2.html.twig');
    }
    /**
     * @Route("/sh17", name="sh17")
     */
    public function sh17(){


        return $this->render('home/restaurant-list.html.twig');
    }

    /**
     * @Route("/sh18", name="sh18")
     */
    public function sh18(){


        return $this->render('home/restaurant-list-isotope.html.twig');
    }

    /**
     * @Route("/sh19", name="sh19")
     */
    public function sh19(){


        return $this->render('home/restaurant-list-sidebar.html.twig');
    }


    /**
     * @Route("/sh20", name="sh20")
     */
    public function sh20(){


        return $this->render('home/restaurant-list-sidebar-2.html.twig');
    }















}
