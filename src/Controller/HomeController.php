<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @var ContactRepository $contactRepository
     */
    private $contactRepository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * HomeController constructor.
     * @param ContactRepository $contactRepository
     * @param EntityManagerInterface $em
     */
    public function __construct(ContactRepository $contactRepository, EntityManagerInterface $em)
    {
        $this->ContactRepository = $contactRepository;
        $this->em = $em;
    }


    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {


        return $this->render('home/index.html.twig', [

        ]);
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contact(Request $request): Response
    {
        if($this->getUser()) {


            $contactEntity = new Contact();
            $form = $this->createForm(ContactType::class, $contactEntity);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {

                $contactEntity->setCreatedAt(new \DateTime());
                $contactEntity->setIsVu(0);
                $contactEntity->setUser($this->getUser());

                $this->em->persist($contactEntity);
                $this->em->flush();
                return $this->redirectToRoute('home');

            }

            return $this->render('home/contact.html.twig', [
                'form' => $form->createView()
            ]);
        }else{
            return $this->redirectToRoute('home');
        }
    }
}
