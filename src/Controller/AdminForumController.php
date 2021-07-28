<?php

namespace App\Controller;

use App\Entity\Forum;
use App\Form\ForumType;
use App\Repository\ForumRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminForumController extends AbstractController
{

    /**
     * @var ForumRepository
     */
    private $forumRepository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * AdminForumController constructor.
     * @param ForumRepository $forumRepository
     * @param EntityManagerInterface $em
     */
    public function __construct(ForumRepository $forumRepository, EntityManagerInterface $em)
    {
        $this->forumRepository = $forumRepository;
        $this->em = $em;
    }

    /**
     * @Route("/admin/forum", name="admin_forum")
     */
    public function index(): Response
    {

        $forumEntities = $this->forumRepository->findAll();
        return $this->render('admin_forum/index.html.twig', [
            'forumEntities' => $forumEntities
        ]);

    }

    /**
     * @Route("/admin/forum_view/{id}", name="admin_forum_read")
     */
    public function view(int $id): Response
    {
        $forumEntity = $this->forumRepository->find($id);

        return $this->render('admin_forum/read.html.twig', [
            'forumEntity' => $forumEntity,
        ]);
    }

    /**
     * @Route("/admin/forum-add", name="admin_forum_add")
     */
    public function add(Request $request):response
    {

        $forumEntity = new Forum();
        $forumEntity->setCreatedAt(new \DateTime());

        $form = $this->createForm(ForumType::class, $forumEntity);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $forumEntity = $form->getData();
            $this->em->persist($forumEntity);
            $this->em->flush();
            return $this->redirectToRoute('admin_forum');
        }

        return $this->render('admin_forum/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/forum-delete/{id}", name="admin_forum_delete")
     */
    public function delete(string $id, Request $request):response
    {

        $forumEntity = $this->forumRepository->find($id);
        $this->em->remove($forumEntity);
        $this->em->flush();
        return $this->redirectToRoute('admin_forum');

    }

    /**
     * @Route("/admin/forum-update/{id}", name="admin_forum_update")
     */
    public function update(string $id, Request $request):response
    {

        $forumEntity = $this->forumRepository->find($id);
        $forumEntity->setCreatedAt(new \DateTime());


        $form = $this->createForm(ForumType::class, $forumEntity);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $forumEntity = $form->getData();
            $this->em->persist($forumEntity);
            $this->em->flush();
            return $this->redirectToRoute('admin_forum');
        }

        return $this->render('admin_forum/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }



}
