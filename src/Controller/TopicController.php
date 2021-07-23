<?php

namespace App\Controller;

use App\Entity\Topic;
use App\Form\TopicType;
use App\Repository\ForumRepository;
use App\Repository\TopicRepository;
use Doctrine\ORM\EntityManagerInterface;
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
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * TopicController constructor.
     * @param TopicRepository $topicRepository
     * @param ForumRepository $forumRepository
     * @param EntityManagerInterface $em
     */
    public function __construct(TopicRepository $topicRepository, ForumRepository $forumRepository, EntityManagerInterface $em)
    {
        $this->topicRepository = $topicRepository;
        $this->forumRepository = $forumRepository;
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
            // this is needed to safely include the file name as part of the URL
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$photo->guessExtension();
                try {
                    $photo->move($this->getParameter('logo_directory'),$newFilename);
                } catch (FileException $e) {
                // unable to upload the photo, give up
                }
                $topicEntity->setPathLogo($newFilename);


            $this->em->persist($topicEntity);
            $this->em->flush();
            return $this->redirectToRoute('forum_view', ['id' => $forum]);
        }

        return $this->render('topic/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/topic_update/{id}", name="topic_update")
     */
    public function update(string $id): Response
    {
        return $this->render('topic/index.html.twig', [

        ]);
    }

    /**
     * @Route("/topic_delete/{id}", name="topic_delete")
     */
    public function delete(string $id): Response
    {
        return $this->render('topic/index.html.twig', [

        ]);
    }

    /**
     * @Route("/topic_view/{id}", name="topic_view")
     */
    public function view(string $id): Response
    {
        $topicEntity = $this->topicRepository->find($id);


        return $this->render('topic/view.html.twig', [
            'topicEntity' => $topicEntity,
        ]);
    }
}
