<?php

namespace App\Command;

use App\Enum\PostEnum;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PostUpdatedCommand extends Command
{
    protected static $defaultName = 'app:post-updated';

    /**
     * @var PostRepository
     */
    private $postRepository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * PostUpdatedCommand constructor.
     * @param PostRepository $userRepository
     * @param EntityManagerInterface $em
     */
    public function __construct(PostRepository $postRepository, EntityManagerInterface $em)
    {
        parent::__construct();
        $this->postRepository = $postRepository;
        $this->em = $em;
    }


    protected function configure(): void
    {
        $this
            ->setDescription('Update an post.')
            ->setHelp('This command allows you to update an post with a date')
            ->addArgument('postDateCreated', InputArgument::REQUIRED, 'the date Created');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $postDateCreated = $input->getArgument('postDateCreated');
        $posts = $this->postRepository->findPostByDate($postDateCreated);

        if(empty($posts))
        {
            $output->writeln("no Post Found");

        }else{
            foreach($posts as $post) {
                $post->setStatus(PostEnum::STATUS_POST_CLOSE);
                $this->em->persist($post);
                $this->em->flush();
            }
            $output->writeln(sizeof($posts)." Posts found and updated !");
        }

        return Command::SUCCESS;
    }
}
