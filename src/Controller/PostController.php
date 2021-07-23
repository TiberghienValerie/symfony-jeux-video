<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    /**
     * @var PostRepository
     */
    private $postRepository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * AdminPostController constructor.
     * @param PostRepository $postRepository
     * @param EntityManagerInterface $em
     */
    public function __construct(PostRepository $postRepository, EntityManagerInterface $em)
    {
        $this->postRepository = $postRepository;
        $this->em = $em;
    }
    /**
     * @Route("/post", name="post")
     */
    public function index(): Response
    {
        $postEntities = $this->postRepository->findAll();

        return $this->render('post/index.html.twig', [
            'postEntities' => $postEntities
        ]);
    }

    /**
     * @Route("/post-read/{id}", name="post_read")
     */
    public function read(string $id): Response
    {
        $postEntity = $this->postRepository->find($id);

        $numberView = $postEntity->getNumberView()+1;
        $postEntity->setNumberView($numberView);
        $this->em->persist($postEntity);
        $this->em->flush();


        return $this->render('post/read.html.twig', [
            'postEntity' => $postEntity
        ]);
    }
}
