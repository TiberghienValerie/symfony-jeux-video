<?php

namespace App\Command;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UpdateUserCommand extends Command
{
    protected static $defaultName = 'app:update-user';

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * UpdateUserCommand constructor.
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $em
     */
    public function __construct(UserRepository $userRepository, EntityManagerInterface $em)
    {
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->em = $em;
    }


    protected function configure(): void
    {
        $this
            ->setDescription('Update an user.')
            ->setHelp('This command allows you to update an user with a email')
            ->addArgument('userEmail', InputArgument::REQUIRED, 'the user email');

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $user_mail = $input->getArgument('userEmail');
        $user = $this->userRepository->findOneBy([
            'email' => $user_mail
        ]);
        if(empty($user))
        {
            $output->writeln("User not found");
        }else{
            $roles = $user->getRoles();
            $data = array();

            foreach($roles as $role)
            {
                $data[] = $role;
            }
            $data[] = "ROLE_WORKER";

            $user->setRoles($data);
            $this->em->persist($user);
            $this->em->flush();

            $output->writeln("l'user ".$user_mail." updated");
        }
        return Command::SUCCESS;

    }
}
