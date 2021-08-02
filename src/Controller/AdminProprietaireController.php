<?php

namespace App\Controller;

use App\Entity\Proprietaire;
use App\Form\ProprietaireType;
use App\Repository\ProprietaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminProprietaireController extends AbstractController
{
    /**
     * @var ProprietaireRepository
     */
    private $proprietaireRepository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * AdminProprietaireController constructor.
     * @param ProprietaireRepository $proprietaireRepository
     * @param EntityManagerInterface $em
     */
    public function __construct(ProprietaireRepository $proprietaireRepository, EntityManagerInterface $em)
    {
        $this->proprietaireRepository = $proprietaireRepository;
        $this->em = $em;
    }


    /**
     * @Route("/admin/proprietaire", name="admin_proprietaire")
     */
    public function index(): Response
    {
        $proprietaireEntities = $this->proprietaireRepository->findAll();

        return $this->render('admin_proprietaire/index.html.twig', [
            'proprietaireEntities' => $proprietaireEntities
        ]);
    }

    /**
     * @Route("/admin/proprietaire-read/{id}", name="admin_proprietaire_read")
     */
    public function read(string $id): Response
    {
        $proprietaireEntity = $this->proprietaireRepository->find($id);

        return $this->render('admin_proprietaire/read.html.twig', [
            'proprietaireEntity' => $proprietaireEntity
        ]);
    }

    /**
     * @Route("/admin/proprietaire-create", name="admin_proprietaire_create")
     */
    public function create(Request $request): Response
    {
        $proprietaireEntity = new Proprietaire();

        $form = $this->createForm(ProprietaireType::class, $proprietaireEntity);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($proprietaireEntity);
            $this->em->flush();
            return $this->redirectToRoute('admin_proprietaire');
        }

        return $this->render('admin_proprietaire/create.html.twig', [
            'form' => $form->CreateView()
        ]);
    }

    /**
     * @Route("/admin/proprietaire-update/{id}", name="admin_proprietaire_update")
     */
    public function update(string $id, Request $request): Response
    {
        $proprietaireEntity = $this->proprietaireRepository->find($id);

        $form = $this->createForm(ProprietaireType::class, $proprietaireEntity);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {


            $this->em->persist($proprietaireEntity);
            $this->em->flush();
            return $this->redirectToRoute('admin_proprietaire');
        }

        return $this->render('admin_proprietaire/update.html.twig', [
            'form' => $form->CreateView()
        ]);
    }

    /**
     * @Route("/admin/proprietaire-delete/{id}", name="admin_proprietaire_delete")
     */
    public function delete(string $id, Request $request): Response
    {
        $proprietaireEntity = $this->proprietaireRepository->find($id);

        $this->em->remove($proprietaireEntity);
        $this->em->flush();
        return $this->redirectToRoute('admin_proprietaire');
    }

}
