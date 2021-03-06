<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Form\RecherchePostType;
use App\Form\RechercheType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminPostController extends AbstractController
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
     * @Route("/admin/post", name="admin_post")
     */
    public function index(PaginatorInterface $paginator,  Request $request): Response
    {
        $qb = $this->postRepository->findPost();

        $form = $this->createForm(RecherchePostType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();

            $qb->innerJoin('p.postCategory', 'pc')
                ->where('p.title LIKE :title')
                ->andWhere('pc.id IN (:array)')
                ->setParameter('title', '%'.$data['objet'].'%')
                ->setParameter('array', $data['postCategory']->getId());

        }
        $pagination = $paginator->paginate(
            $qb, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('admin_post/index.html.twig', [
            'pagination' => $pagination,
            'form' => $form->createView()
        ]);


    }

    /**
     * @Route("/admin/post-read/{id}", name="admin_post_read")
     */
    public function read(string $id): Response
    {
        $postEntity = $this->postRepository->find($id);

        return $this->render('admin_post/read.html.twig', [
            'postEntity' => $postEntity
        ]);
    }

    /**
     * @Route("/admin/post-create", name="admin_post_create")
     */
    public function create(Request $request): Response
    {
        $postEntity = new Post();
        $postEntity->setCreatedAt(new \DateTime());
        $postEntity->setUser($this->getUser());
        $postEntity->setStatus(0);
        $postEntity->setNumberView(0);

        $form = $this->createForm(PostType::class, $postEntity);



        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($postEntity);
            $this->em->flush();
            return $this->redirectToRoute('admin_post');
        }

        return $this->render('admin_post/create.html.twig', [
            'form' => $form->CreateView()
        ]);
    }

    /**
     * @Route("/admin/post-update/{id}", name="admin_post_update")
     */
    public function update(string $id, Request $request): Response
    {
        $postEntity = $this->postRepository->find($id);

        $form = $this->createForm(PostType::class, $postEntity);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($postEntity);
            $this->em->flush();
            return $this->redirectToRoute('admin_post');
        }

        return $this->render('admin_post/update.html.twig', [
            'form' => $form->CreateView()
        ]);
    }

    /**
     * @Route("/admin/post-delete/{id}", name="admin_post_delete")
     */
    public function delete(string $id, Request $request): Response
    {
        $postEntity = $this->postRepository->find($id);
        $this->em->remove($postEntity);
        $this->em->flush();
        return $this->redirectToRoute('admin_post');
    }
}
