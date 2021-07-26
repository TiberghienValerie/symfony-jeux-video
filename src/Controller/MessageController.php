<?php

namespace App\Controller;

use App\Form\MessageType;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class MessageController extends AbstractController
{
    /**
     * @var MessageRepository $messageRepository
     */
    private $messageRepository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * MessageController constructor.
     * @param MessageRepository $messageRepository
     * @param EntityManagerInterface $em
     */
    public function __construct(MessageRepository $messageRepository, EntityManagerInterface $em)
    {
        $this->messageRepository = $messageRepository;
        $this->em = $em;
    }

    /**
     * @Route("/message_view/{id}", name="message_view")
     */
    public function view(string $id, Request $request): response
    {
        $messageEntity = $this->messageRepository->find($id);
        return $this->render('message/view.html.twig', [
            'messageEntity' => $messageEntity
        ]);
    }

    /**
     * @Route("/message_update/{id}", name="message_update")
     */
    public function update(string $id, Request $request): Response
    {
        $messageEntity = $this->messageRepository->find($id);

        $form = $this->createForm(MessageType::class, $messageEntity);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->em->persist($messageEntity);
            $this->em->flush();
            return $this->redirectToRoute('topic_view', ['id' => $messageEntity->getTopic()->getId()]);
        }

        return $this->render('message/update.html.twig', [
            'form' => $form->createView(),
            'topic' => $messageEntity->getTopic()->getId(),
        ]);

    }

    /**
     * @Route("/message_delete/{id}", name="message_delete")
     */
    public function delete(string $id, Request $request): Response
    {
        $messageEntity = $this->messageRepository->find($id);
        $this->em->remove($messageEntity);
        $this->em->flush();
        return $this->redirectToRoute('topic_view', ['id' => $messageEntity->getTopic()->getId()]);
    }
}
