<?php

namespace App\Controller;

use App\Entity\Device;
use App\Form\DeviceType;
use App\Repository\DeviceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class AdminDeviceController extends AbstractController
{
    /**
     * @var DeviceRepository
     */
    private $devicerepository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * AdminDeviceController constructor.
     * @param DeviceRepository $devicerepository
     * @param EntityManagerInterface $em
     */
    public function __construct(DeviceRepository $devicerepository, EntityManagerInterface $em)
    {
        $this->devicerepository = $devicerepository;
        $this->em = $em;
    }


    /**
     * @Route("/admin/device", name="admin_device")
     */
    public function index(): Response
    {
        $deviceEntities =  $this->devicerepository->findAll();

        return $this->render('admin_device/index.html.twig', [
            'deviceEntities' => $deviceEntities
        ]);
    }


    /**
     * @Route("/admin/device-read/{id}", name="admin_device_read")
     */
    public function read(string $id): Response
    {
        $deviceEntity = $this->devicerepository->find($id);

        return $this->render('admin_device/read.html.twig', [
            'deviceEntity' => $deviceEntity
        ]);
    }

    /**
     * @Route("/admin/device-create", name="admin_device_create")
     */
    public function create(Request $request, SluggerInterface $slugger): Response
    {
        $deviceEntity = new Device();
        $form = $this->createForm(DeviceType::class, $deviceEntity);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $photo = $form->get('pathLogo')->getData();
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
            $deviceEntity->setPathLogo($newFilename);
            $this->em->persist($deviceEntity);
            $this->em->flush();
            return $this->redirectToRoute('admin_device');
        }

        return $this->render('admin_device/create.html.twig', [
            'form' => $form->CreateView()
        ]);
    }

    /**
     * @Route("/admin/device-update/{id}", name="admin_device_update")
     */
    public function update(string $id, Request $request, SluggerInterface $slugger): Response
    {
        $deviceEntity = $this->devicerepository->find($id);

        $form = $this->createForm(DeviceType::class, $deviceEntity);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $photo = $form->get('pathLogo')->getData();
            if($photo !== null) {

                $ancienPhoto = $deviceEntity->getPathLogo();
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
                $deviceEntity->setPathLogo($newFilename);
            }

            $this->em->persist($deviceEntity);
            $this->em->flush();
            return $this->redirectToRoute('admin_device');
        }

        return $this->render('admin_device/update.html.twig', [
            'form' => $form->CreateView(),
            'logo' => $deviceEntity->getPathLogo()
        ]);
    }

    /**
     * @Route("/admin/device-delete/{id}", name="admin_device_delete")
     */
    public function delete(string $id, Request $request): Response
    {
        $deviceEntity = $this->devicerepository->find($id);
        $ancienPhoto =  $deviceEntity->getPathLogo();
        if (file_exists($this->getParameter('logo_directory').'/'.$ancienPhoto)) {
            unlink($this->getParameter('logo_directory').'/'.$ancienPhoto);
        }
        $this->em->remove($deviceEntity);
        $this->em->flush();
        return $this->redirectToRoute('admin_device');
    }








}
