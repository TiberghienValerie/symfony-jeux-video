<?php


namespace App\Twig;


use App\Repository\ContactRepository;
use App\Repository\GameRepository;
use App\Repository\PostRepository;
use App\Repository\ProprietaireRepository;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TwigExtension extends AbstractExtension
{
    /**
     * @var GameRepository
     */
    private $gameRepository;

    /**
     * @var ProprietaireRepository
     */
    private $proprietaireRepository;

    /**
     * @var PostRepository
     */
    private $postRepository;

    /**
     * @var ContactRepository
     */
    private $contactRepository;

     /**
     * @var Environment
     */
    private $twigEnvironnement;

    /**
     * TwigExtension constructor.
     * @param GameRepository $gameRepository
     * @param ProprietaireRepository $proprietaireRepository
     * @param PostRepository $postRepository
     * @param ContactRepository $contactRepository
     * @param Environment $twigEnvironnement
     */
    public function __construct(GameRepository $gameRepository, ProprietaireRepository $proprietaireRepository, PostRepository $postRepository, ContactRepository $contactRepository, Environment $twigEnvironnement)
    {
        $this->gameRepository = $gameRepository;
        $this->proprietaireRepository = $proprietaireRepository;
        $this->postRepository = $postRepository;
        $this->contactRepository = $contactRepository;
        $this->twigEnvironnement = $twigEnvironnement;
    }


    public function getFilters()
    {
        return [

        ];
    }

    public function getFunctions() {
        return [
            new TwigFunction('generate_etoile_html', [$this, 'generateEtoileHtml']),
            new TwigFunction('generate_coordonnees_html', [$this, 'generateCoordonneesHtml']),
            new TwigFunction('nb_post', [$this, 'nbPost']),
            new TwigFunction('nb_game', [$this, 'nbGame']),
            new TwigFunction('nb_message_non_lu', [$this, 'nbMessageNonLu']),
            new TwigFunction('generate_notification_html', [$this, 'generateNotificationHtml']),

        ];
    }

    public function generateEtoileHtml($gameEntity)
    {

        return $this->twigEnvironnement->render('partial/etoile-html.html.twig', [
            'gameEntity' => $gameEntity
        ]);
    }

    public function generateCoordonneesHtml() {
        $proprietaireEntities = $this->proprietaireRepository->findAll();
        return $this->twigEnvironnement->render('partial/coordonnees.html.twig', [
            'proprietaireEntities' => $proprietaireEntities
        ]);
    }

    public function nbPost()
    {
        return sizeof($this->postRepository->findAll());
    }

     public function nbGame()
     {
         return sizeof($this->gameRepository->findAll());
     }

    public function nbMessageNonLu()
    {
        return sizeof($this->contactRepository->findBy([
            'status' => 0
        ]));
    }

    public function generateNotificationHtml() {
        $contactEntities = $this->contactRepository->findBy([
            'status' => 0
        ]);
        return $this->twigEnvironnement->render('partial/notification.html.twig', [
            'contactEntities' => $contactEntities
        ]);
    }









}