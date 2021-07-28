<?php

namespace App\Controller;

use App\Entity\Game;
use App\Form\GameType;
use App\Repository\GameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class AdminGameController extends AbstractController
{

    /**
     * @var GameRepository
     */
    private $gameRepository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * AdminGameController constructor.
     * @param GameRepository $gameRepository
     * @param EntityManagerInterface $em
     */
    public function __construct(GameRepository $gameRepository, EntityManagerInterface $em)
    {
        $this->gameRepository = $gameRepository;
        $this->em = $em;
    }

    /**
     * @Route("/admin/game", name="admin_game")
     */
    public function index(): Response
    {
        $gameEntities = $this->gameRepository->findAll();
        return $this->render('admin_game/index.html.twig', [
            'gameEntities' => $gameEntities
        ]);
    }

    /**
     * @Route("/admin/game-read/{id}", name="admin_game_read")
     */
    public function read(string $id): Response
    {
        $gameEntity = $this->gameRepository->find($id);

        return $this->render('admin_game/read.html.twig', [
            'gameEntity' => $gameEntity
        ]);
    }

    /**
     * @Route("/admin/game-create", name="admin_game_create")
     */
    public function create(Request $request,  SluggerInterface $slugger): Response
    {
        $gameEntity = new Game();

        $form = $this->createForm(GameType::class, $gameEntity);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $photo = $form->get('pathImg')->getData();
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
            $gameEntity->setPathImg($newFilename);
            $gameEntity->setLaunchAt(new \DateTime());
            //$gameEntity->setNoteGlobal(1);

            $this->em->persist($gameEntity);
            $this->em->flush();
            return $this->redirectToRoute('admin_game');
        }

        return $this->render('admin_game/create.html.twig', [
            'form' => $form->CreateView()
        ]);
    }

    /**
     * @Route("/admin/game-update/{id}", name="admin_game_update")
     */
    public function update(string $id, Request $request,  SluggerInterface $slugger): Response
    {
        $gameEntity = $this->gameRepository->find($id);

        $form = $this->createForm(GameType::class, $gameEntity);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $photo = $form->get('pathImg')->getData();
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
            $ancienPhoto =  $gameEntity->getPathImg();
            if (file_exists($this->getParameter('logo_directory').'/'.$ancienPhoto)) {
                unlink($this->getParameter('logo_directory').'/'.$ancienPhoto);
            }
            $gameEntity->setPathImg($newFilename);


            $this->em->persist($gameEntity);
            $this->em->flush();
            return $this->redirectToRoute('admin_game');
        }

        return $this->render('admin_game/update.html.twig', [
            'form' => $form->CreateView(),
            'logo' => $gameEntity->getPathImg()
        ]);
    }

    /**
     * @Route("/admin/game-delete/{id}", name="admin_game_delete")
     */
    public function delete(string $id, Request $request): Response
    {
        $gameEntity = $this->gameRepository->find($id);
        $ancienPhoto =  $gameEntity->getPathImg();
        if (file_exists($this->getParameter('logo_directory').'/'.$ancienPhoto)) {
            unlink($this->getParameter('logo_directory').'/'.$ancienPhoto);
        }
        $this->em->remove($gameEntity);
        $this->em->flush();
        return $this->redirectToRoute('admin_game');
    }



}
