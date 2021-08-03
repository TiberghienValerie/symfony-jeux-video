<?php

namespace App\Controller;

use App\Entity\PostCategory;
use App\Form\PostCategoryType;
use App\Form\RechercheType;
use App\Repository\PostCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminPostCategoryController extends AbstractController
{
    /**
     * @var PostCategoryRepository
     */
    private $postCategoryRepository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * AdminPostCategoryController constructor.
     * @param PostCategoryRepository $postCategoryRepository
     * @param EntityManagerInterface $em
     */
    public function __construct(PostCategoryRepository $postCategoryRepository, EntityManagerInterface $em)
    {
        $this->postCategoryRepository = $postCategoryRepository;
        $this->em = $em;
    }


    /**
     * @Route("/admin/post-category", name="admin_post_category")
     */
    public function index(PaginatorInterface $paginator,  Request $request): Response
    {
        $qb = $this->postCategoryRepository->findPostCategory();

        $form = $this->createForm(RechercheType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $qb->where('pc.name LIKE :name')
                ->setParameter('name', '%'.$data['objet'].'%');
        }
        $pagination = $paginator->paginate(
            $qb, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('admin_post_category/index.html.twig', [
            'pagination' => $pagination,
            'form' => $form->createView()
        ]);

    }

    /**
     * @Route("/admin/post-category-read/{id}", name="admin_post_category_read")
     */
    public function read(string $id): Response
    {
        $postCategoryEntity = $this->postCategoryRepository->find($id);

        return $this->render('admin_post_category/read.html.twig', [
            'postCategoryEntity' => $postCategoryEntity
        ]);
    }

    /**
     * @Route("/admin/post-category-create", name="admin_post_category_create")
     */
    public function create(Request $request): Response
    {
        $postCategoryEntity = new PostCategory();

        $form = $this->createForm(PostCategoryType::class, $postCategoryEntity);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($postCategoryEntity);
            $this->em->flush();
            return $this->redirectToRoute('admin_post_category');
        }

        return $this->render('admin_post_category/create.html.twig', [
            'form' => $form->CreateView()
        ]);
    }

    /**
     * @Route("/admin/post-category-update/{id}", name="admin_post_category_update")
     */
    public function update(string $id, Request $request): Response
    {
        $postCategoryEntity = $this->postCategoryRepository->find($id);

        $form = $this->createForm(PostCategoryType::class, $postCategoryEntity);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($postCategoryEntity);
            $this->em->flush();
            return $this->redirectToRoute('admin_post_category');
        }

        return $this->render('admin_post_category/update.html.twig', [
            'form' => $form->CreateView()
        ]);
    }

    /**
     * @Route("/admin/post-category-delete/{id}", name="admin_post_category_delete")
     */
    public function delete(string $id, Request $request): Response
    {
        $postCategoryEntity = $this->postCategoryRepository->find($id);
        $this->em->remove($postCategoryEntity);
        $this->em->flush();
        return $this->redirectToRoute('admin_post_category');
    }
}
