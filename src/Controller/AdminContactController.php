<?php

namespace App\Controller;

use App\Enum\ContactEnum;
use App\Form\RechercheType;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    public function index(PaginatorInterface $paginator,  Request $request): Response
    {
        $qb = $this->contactRepository->findContact();

        $form = $this->createForm(RechercheType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $qb->where('c.objet LIKE :objet')
                ->setParameter('objet', '%'.$data['objet'].'%');
        }
        $pagination = $paginator->paginate(
            $qb, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('admin_contact/index.html.twig', [
            'pagination' => $pagination,
            'form' => $form->createView()
        ]);


    }

    /**
     * @Route("/admin/contact-read/{id}", name="admin_contact_read")
     */
    public function read(string $id): Response
    {
        $contactEntity = $this->contactRepository->find($id);
        //$contactEntity->setIsVu(1);
        $contactEntity->setStatus(ContactEnum::STATUS_TICKET_READ);
        $this->em->persist($contactEntity);
        $this->em->flush();

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
        $this->em->persist($contactEntity);
        $this->em->flush();
        return $this->redirectToRoute('admin_contact');


    }
}
