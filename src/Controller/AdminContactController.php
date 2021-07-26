<?php

namespace App\Controller;

use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminContactController extends AbstractController
{
    /**
     * @var ContactRepository
     */
    private $contactRepository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * AdminContactController constructor.
     * @param ContactRepository $contactRepository
     * @param EntityManagerInterface $em
     */
    public function __construct(ContactRepository $contactRepository, EntityManagerInterface $em)
    {
        $this->contactRepository = $contactRepository;
        $this->em = $em;
    }


    /**
     * @Route("/admin/contact", name="admin_contact")
     */
    public function index(): Response
    {
        $contactEntities = $this->contactRepository->findAll();

        return $this->render('admin_contact/index.html.twig', [
            'contactEntities' => $contactEntities
        ]);
    }

    /**
     * @Route("/admin/contact-read/{id}", name="admin_contact_read")
     */
    public function read(string $id): Response
    {
        $contactEntity = $this->contactRepository->find($id);

        return $this->render('admin_contact/read.html.twig', [
            'contactEntity' => $contactEntity
        ]);
    }

    /**
     * @Route("/admin/contact-traiter/{id}", name="admin_contact_traiter")
     */
    public function traiter(string $id): Response
    {
        $contactEntity = $this->contactRepository->find($id);
        $contactEntity->setTraitedAt(new \DateTime());
        $contactEntity->setIsVu(1);
        $this->em->persist($contactEntity);
        $this->em->flush();
        return $this->redirectToRoute('admin_contact');


    }
}
