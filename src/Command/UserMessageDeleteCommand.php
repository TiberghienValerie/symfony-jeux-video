<?php

namespace App\Command;

use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UserMessageDeleteCommand extends Command
{

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var MessageRepository
     */
    private $messageRepository;


    const MOT_BANNIS = ['horrible', 'moche', 'idiot'];

    protected static $defaultName = 'app:user-message-delete';

    /**
     * UserMessageDeleteCommand constructor.
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $em
     * @param MessageRepository $messageRepository
     */
    public function __construct(UserRepository $userRepository, EntityManagerInterface $em, MessageRepository $messageRepository)
    {
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->em = $em;
        $this->messageRepository = $messageRepository;
    }


    protected function configure(): void
    {
        $this
            ->setDescription('delete an user en fonction du nombre de messages.')
            ->setHelp('This command allows you to delete user si message pas bon');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /* Récupération message non checké */
        $messages = $this->messageRepository->findMessageNotChecked();
        /* MAJ des nb de fautes à 0 à tous les utilisateurs */
        $user = $this->userRepository->findAll();
        foreach($user as $u) {
            $u->setNbfaute(0);
            $this->em->persist($u);
            $this->em->flush();
        }

        /* pour chaque message */
        foreach ($messages as $message) {

            // Récupération du nbfaute du user et compteur à 0
            $countUser = $message->getUser()->getNbFaute();
            $count=0;
            /* Vérification si des mots bannis ou pas dans le message*/
            foreach (self::MOT_BANNIS as $motBanni) {
                /* si c'est oui, cumul du compteur */
                if (str_contains($message->getContent(), $motBanni)) {
                    $count++;
                }
            }
            //Mise à jour du nombre de fautes dans le user et du checked à 1
            $nbfaute = $countUser+$count;
            $message->getUser()->setNbFaute($nbfaute);
            $message->setIsChecked(1);
            $this->em->persist($message->getUser());
            $this->em->persist($message);
            $this->em->flush();
        }

        // Récupération des users dont le nb de faute >= 3
        $userFaute = $this->userRepository->findUser();
        foreach($userFaute as $u) {
            $u->setIsBanni(1);
            $this->em->persist($u);
            $this->em->flush();
        }
        //retourne le nb d'utilisateur banni
        if(sizeof($userFaute) > 0) {
            $output->writeln(sizeof($userFaute) . " users banni(s)");
        }else{
            $output->writeln("Aucun user banni");
        }
        return Command::SUCCESS;
    }
}
