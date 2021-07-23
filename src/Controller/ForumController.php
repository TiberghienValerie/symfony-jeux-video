<?php

namespace App\Controller;

use App\Entity\Forum;
use App\Form\ForumType;
use App\Repository\ForumRepository;
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
     * @param TopicRepository $topicRepository
     * @param EntityManagerInterface $em
     */
    public function __construct(ForumRepository $forumRepository, TopicRepository $topicRepository, EntityManagerInterface $em)
    {
        $this->forumRepository = $forumRepository;
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

        return $this->render('forum/view.html.twig', [
            'forumEntity' => $forumEntity,
            'topicEntities' => $topicEntities
        ]);
    }

    /**
     * @Route("/forum_add", name="forum_add")
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
            return $this->redirectToRoute('forum_list');
        }

        return $this->render('forum/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/forum_delete/{id}", name="forum_delete")
     */
    public function delete(string $id, Request $request):response
    {

        $forumEntity = $this->forumRepository->find($id);
        $this->em->remove($forumEntity);
        $this->em->flush();
        return $this->redirectToRoute('forum_list');

    }

    /**
     * @Route("/forum_update/{id}", name="forum_update")
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
            return $this->redirectToRoute('forum_list');
        }

        return $this->render('forum/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
