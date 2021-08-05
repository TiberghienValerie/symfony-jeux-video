<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Topic;
use App\Form\MessageType;
use App\Form\TopicType;
use App\Repository\ForumRepository;
use App\Repository\MessageRepository;
use App\Repository\TopicRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;



class TopicController extends AbstractController
{
    /**
     * @var TopicRepository
     */
    private $topicRepository;

    /**
     * @var ForumRepository
     */
    private $forumRepository;

    /**
     * @var MessageRepository
     */
    private $messagerepository;

    /**
     * @var EntityManagerInterface
     */
    private $em;


    /**
     * TopicController constructor.
     * @param MessageRepository $messageRepository
     * @param TopicRepository $topicRepository
     * @param ForumRepository $forumRepository
     * @param EntityManagerInterface $em
     */
    public function __construct(MessageRepository $messageRepository, TopicRepository $topicRepository, ForumRepository $forumRepository, EntityManagerInterface $em)
    {
        $this->topicRepository = $topicRepository;
        $this->forumRepository = $forumRepository;
        $this->messageRepository = $messageRepository;
        $this->em = $em;
    }

    /**
     * @Route("/topic_add/{forum}", name="topic_add")
     */
    public function add(string $forum, Request $request, SluggerInterface $slugger): Response
    {
        $forumEntity = $this->forumRepository->find($forum);
        $topicEntity = new Topic();
        $topicEntity->setCreatedAt(new \DateTime());
        $topicEntity->setForum($forumEntity);

        $form = $this->createForm(TopicType::class, $topicEntity);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $photo = $form->get('pathLogo')->getData();
            $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'.'.$photo->guessExtension();
            if (!file_exists($this->getParameter('logo_directory').'/'.$newFilename)) {
                try {
                    $photo->move($this->getParameter('logo_directory'), $newFilename);
                } catch (FileException $e) {
                    // unable to upload the photo, give up
                }
            }
            $topicEntity->setPathLogo($newFilename);
            $this->em->persist($topicEntity);
            $this->em->flush();
            return $this->redirectToRoute('forum_view', ['id' => $forum]);
        }

        return $this->render('topic/add.html.twig', [
            'form' => $form->createView(),
            'id' => $forum
        ]);
    }

    /**
     * @Route("/topic_update/{id}", name="topic_update")
     */
    public function update(string $id, Request $request, SluggerInterface $slugger): Response
    {
        $topicEntity = $this->topicRepository->find($id);

        $form = $this->createForm(TopicType::class, $topicEntity);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $photo = $form->get('pathLogo')->getData();
            $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'.'.$photo->guessExtension();
            if (!file_exists($this->getParameter('logo_directory').'/'.$newFilename)) {
                try {
                    $photo->move($this->getParameter('logo_directory'), $newFilename);
                } catch (FileException $e) {
                    // unable to upload the photo, give up
                }
            }
            $topicEntity->setPathLogo($newFilename);
            $this->em->persist($topicEntity);
            $this->em->flush();
            return $this->redirectToRoute('forum_view', ['id' => $topicEntity->getForum()->getId()]);
        }


        return $this->render('topic/update.html.twig', [
            'form' => $form->createView(),
            'forum' => $topicEntity->getForum()->getId(),
            'logo' => $topicEntity->getPathLogo()
        ]);
    }

    /**
     * @Route("/topic_delete/{id}", name="topic_delete")
     */
    public function delete(string $id, Request $request): Response
    {
         $topicEntity = $this->topicRepository->find($id);
         $this->em->remove($topicEntity);
         $this->em->flush();
         return $this->redirectToRoute('forum_view', ['id' => $topicEntity->getForum()->getId()]);
    }

    /**
     * @Route("/topic_view/{id}", name="topic_view")
     */
    public function view(string $id, Request $request, PaginatorInterface $paginator): Response
    {
        $topicEntity = $this->topicRepository->find($id);

        $qb = $this->messageRepository->findMessage($topicEntity);

        $pagination = $paginator->paginate(
            $qb, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );




        $messageEntity = new Message();
        $messageEntity->setCreatedAt(new \DateTime());
        $messageEntity->setIsChecked(0);
        $messageEntity->setTopic($topicEntity);
        $messageEntity->setUser($this->getUser());

        $form = $this->createForm(MessageType::class, $messageEntity);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->em->persist($messageEntity);
            $this->em->flush();
            return $this->redirectToRoute('topic_view', ['id' => $id]);
        }

        return $this->render('topic/view.html.twig', [
            'form' => $form->createView(),
            'topicEntity' => $topicEntity,
            /*'messageEntities' => $messageEntities,*/
            'pagination' => $pagination

        ]);
    }
}
