<?php

namespace App\Controller;

use App\Entity\Forum;
use App\Form\ForumType;
use App\Repository\ForumRepository;
use App\Repository\GameRepository;
use App\Repository\TopicRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ForumController extends AbstractController
{
    /**
     * @var ForumRepository
     */
    private $forumRepository;

    /**
     * @var GameRepository
     */
    private $gameRepository;

    /**
     * @var TopicRepository
     */
    private $topicRepository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * ForumController constructor.
     * @param ForumRepository $forumRepository
     * @param GameRepository $gameRepository
     * @param TopicRepository $topicRepository
     * @param EntityManagerInterface $em
     */
    public function __construct(ForumRepository $forumRepository, GameRepository $gameRepository, TopicRepository $topicRepository, EntityManagerInterface $em)
    {
        $this->forumRepository = $forumRepository;
        $this->gameRepository = $gameRepository;
        $this->topicRepository = $topicRepository;
        $this->em = $em;
    }


    /**
     * @Route("/forum", name="forum_list")
     */
    public function index(): Response
    {
        $forumEntities = $this->forumRepository->findAll();
        return $this->render('forum/index.html.twig', [
            'forumEntities' => $forumEntities
        ]);
    }

    /**
     * @Route("/forum_view/{id}", name="forum_view")
     */
    public function view(int $id): Response
    {
        $forumEntity = $this->forumRepository->find($id);
        $topicEntities = $this->topicRepository->findBy(
            ['forum'=>$id]
        );

        $gameEntities = $this->gameRepository->findBy(
            ['forum'=>$id]
        );

        return $this->render('forum/view.html.twig', [
            'forumEntity' => $forumEntity,
            'topicEntities' => $topicEntities,
            'gameEntities' => $gameEntities
        ]);
    }

}
