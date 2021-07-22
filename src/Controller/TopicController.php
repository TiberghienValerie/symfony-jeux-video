<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TopicController extends AbstractController
{
    /**
     * @Route("/topic_add", name="topic_add")
     */
    public function add(): Response
    {
        return $this->render('topic/index.html.twig', [

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
        return $this->render('topic/index.html.twig', [

        ]);
    }
}
