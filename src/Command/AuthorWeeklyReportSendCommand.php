<?php

namespace App\Command;

use App\Repository\ChambreRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\Bridge\Google\Transport\GmailSmtpTransport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

use Symfony\Component\Mime\Address;


class AuthorWeeklyReportSendCommand extends Command
{
    protected static $defaultName = 'app:author-weekly-report:send';
    protected static $defaultDescription = 'Add a short description for your command';
    private  $chambreRepository;
private  $mailer;
    public function __construct(ChambreRepository $chambreRepository,MailerInterface $mailer)
    {
        parent::__construct(null);
        $this->chambreRepository = $chambreRepository;
        $this->mailer = $mailer;

    }

    protected function configure()
    {
        $this
            ->setDescription(self::$defaultDescription)

        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {


        $io = new SymfonyStyle($input, $output);

        $authors = $this->chambreRepository
            ->showChambreExpire();
        $io->progressStart(count($authors));

            $io->progressAdvance();

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

        $this->mailer->send($email);

        $io->progressFinish();
        $io->success('temchi!');
        return 0;

    }

}
