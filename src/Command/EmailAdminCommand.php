<?php

namespace App\Command;

use App\Repository\ChambreRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class EmailAdminCommand extends Command
{
    protected static $defaultName = 'EmailAdmin';
    protected static $defaultDescription = 'Add a short description for your command';
    private  $chambreRepository;
    /**
     * EmailAdminCommand constructor.
     */
    public function __construct(ChambreRepository $chambreRepository)
    {
        parent::__construct(null);
        $this->chambreRepository = $chambreRepository;
    }

    protected function configure()
    {
        $this
            ->setDescription("chambre libre report")

        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $authors = $this->chambreRepository
            ->showChambreExpire();
        $io->progressStart(count($authors));
        foreach ($authors as $author) {
            $io->progressAdvance();
        }
        $io->progressFinish();
        $io->success('Weekly reports were sent to authors!');
        return 0;

    }
}
