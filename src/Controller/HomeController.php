<?php

namespace App\Controller;

use App\Repository\ChambreRepository;
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
    public function index(MailerInterface $mailer,ChambreRepository $chambreRepository): Response
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
//        $transport = new GmailSmtpTransport('saieftaher1','saief1998');
//        $mailer = new Mailer($transport);

        $mailer->send($email);
    }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
