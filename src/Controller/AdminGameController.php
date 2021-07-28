<?php

namespace App\Controller;

use App\Entity\Forum;
use App\Entity\Game;
use App\Form\ForumType;
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

        $form = $this->createForm(Game_Admin_Type::class, $gameEntity);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $photo = $form->get('pathImg')->getData();
            if($photo !== null) {

                $ancienPhoto = $gameEntity->getPathImg();
                if (file_exists($this->getParameter('logo_directory') . '/' . $ancienPhoto)) {
                    try {
                        unlink($this->getParameter('logo_directory') . '/' . $ancienPhoto);
                    } catch (FileException $e) {
                        // unable to upload the photo, give up
                    }
                }

                $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '.' . $photo->guessExtension();
                if (!file_exists($this->getParameter('logo_directory') . '/' . $newFilename)) {
                    try {
                        $photo->move($this->getParameter('logo_directory'), $newFilename);
                    } catch (FileException $e) {
                        // unable to upload the photo, give up
                    }
                }
                $gameEntity->setPathImg($newFilename);
            }


            $this->em->persist($gameEntity);
            $this->em->flush();
            return $this->redirectToRoute('admin_game');
        }

        return $this->render('admin_game/update.html.twig', [
            'form' => $form->CreateView(),
            'logo' => $gameEntity->getPathImg(),
            'noteGlobal' => $gameEntity->getNoteGlobal(),
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



    /**
     * @Route("/admin/forum-add-game/{id}", name="admin_forum_add_game")
     */
    public function add(string $id, Request $request):response
    {

        $gameEntity = $this->gameRepository->find($id);
        $forumEntity = new Forum();
        $forumEntity->setCreatedAt(new \DateTime());
        $form = $this->createForm(ForumType::class, $forumEntity);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $forumEntity = $form->getData();
            $this->em->persist($forumEntity);
            $this->em->flush();

            $gameEntity->setForum($forumEntity);
            $this->em->persist($gameEntity);
            $this->em->flush();

            return $this->redirectToRoute('admin_game');
        }

        return $this->render('admin_game/create_forum.html.twig', [
            'form' => $form->createView(),
        ]);
    }




}
