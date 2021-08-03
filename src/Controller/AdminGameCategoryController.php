<?php

namespace App\Controller;

use App\Entity\GameCategory;
use App\Form\GameCategoryType;
use App\Form\RechercheType;
use App\Repository\GameCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminGameCategoryController extends AbstractController
{
    /**
     * @var GameCategoryRepository
     */
    private $gameCategoryRepository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * AdminGameCategoryController constructor.
     * @param GameCategoryRepository $gameCategoryRepository
     * @param EntityManagerInterface $em
     */
    public function __construct(GameCategoryRepository $gameCategoryRepository, EntityManagerInterface $em)
    {
        $this->gameCategoryRepository = $gameCategoryRepository;
        $this->em = $em;
    }


    /**
     * @Route("/admin/game-category", name="admin_game_category")
     */

        public function index(PaginatorInterface $paginator,  Request $request): Response
    {
        $qb = $this->gameCategoryRepository->findGameCategory();

        $form = $this->createForm(RechercheType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $qb->where('gc.name LIKE :name')
                ->setParameter('name', '%'.$data['objet'].'%');
        }
        $pagination = $paginator->paginate(
            $qb, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('admin_game_category/index.html.twig', [
            'pagination' => $pagination,
            'form' => $form->createView()
        ]);


    }




    /**
     * @Route("/admin/game-category-read/{id}", name="admin_game_category_read")
     */
    public function read(string $id): Response
    {
        $gameCategoryEntity = $this->gameCategoryRepository->find($id);

        return $this->render('admin_game_category/read.html.twig', [
            'gameCategoryEntity' => $gameCategoryEntity
        ]);
    }

    /**
     * @Route("/admin/game-category-create", name="admin_game_category_create")
     */
    public function create(Request $request): Response
    {
        $gameCategoryEntity = new GameCategory();

        $form = $this->createForm(GameCategoryType::class, $gameCategoryEntity);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($gameCategoryEntity);
            $this->em->flush();
            return $this->redirectToRoute('admin_game_category');
        }

        return $this->render('admin_game_category/create.html.twig', [
            'form' => $form->CreateView()
        ]);
    }

    /**
     * @Route("/admin/game-category-update/{id}", name="admin_game_category_update")
     */
    public function update(string $id, Request $request): Response
    {
        $gameCategoryEntity = $this->gameCategoryRepository->find($id);

        $form = $this->createForm(GameCategoryType::class, $gameCategoryEntity);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($gameCategoryEntity);
            $this->em->flush();
            return $this->redirectToRoute('admin_game_category');
        }

        return $this->render('admin_game_category/update.html.twig', [
            'form' => $form->CreateView()
        ]);
    }

    /**
     * @Route("/admin/game-category-delete/{id}", name="admin_game_category_delete")
     */
    public function delete(string $id, Request $request): Response
    {
        $gameCategoryEntity = $this->gameCategoryRepository->find($id);
        $this->em->remove($gameCategoryEntity);
        $this->em->flush();
        return $this->redirectToRoute('admin_game_category');
    }
}
