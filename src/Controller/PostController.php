<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        //$postEntities = $this->postRepository->findAll();
        $qb = $this->postRepository->findPost();

        $pagination = $paginator->paginate(
            $qb, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
        return $this->render('post/index.html.twig', [
            'pagination' => $pagination
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
