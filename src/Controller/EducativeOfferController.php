<?php

namespace App\Controller;

use App\Entity\EducativeOffer;
use App\Form\EducativeOfferType;
use App\Repository\EducativeOfferRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Filesystem\Filesystem;

#[Route('/educative_offer')]
class EducativeOfferController extends AbstractController
{
    #[Route('/', name: 'app_educative_offer_index', methods: ['GET'])]
    public function index(EducativeOfferRepository $educativeOfferRepository): Response
    {
        return $this->render('educative_offer/index.html.twig', [
            /* 'educative_offer' => $educativeOfferRepository->findAll(), */ //Orden de creación
            'educative_offers' => $educativeOfferRepository->findBy([], ['id' => 'DESC']),//Más reciente primero
        ]);
    }

    #[Route('/new', name: 'app_educative_offer_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $educativeOffer = new EducativeOffer();

        $form = $this->createForm(EducativeOfferType::class, $educativeOffer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                $extension = strtolower($imageFile->getClientOriginalExtension());

                if (!in_array($extension, $allowedExtensions)) {
                    $this->addFlash('error', 'Solo se permiten imágenes JPG, PNG o GIF');
                } else {
                    $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $extension;
                    try {
                        $imageFile->move(
                            $this->getParameter('images_directory_organization_chart'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        $this->addFlash('error', 'No se pudo subir la imagen: ' . $e->getMessage());
                    }
                    $educativeOffer->setImage($newFilename);
                }
            }

            $entityManager->persist($educativeOffer);
            $entityManager->flush();

            return $this->redirect('/educative_offer', Response::HTTP_SEE_OTHER);
        }

        return $this->render('educative_offer/new.html.twig', [
            'educative_offer' => $educativeOffer,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_educative_offer_show', methods: ['GET'])]
    public function show(EducativeOffer $educativeOffer): Response
    {
        return $this->render('educative_offer/show.html.twig', [
            'educative_offer' => $educativeOffer,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_educative_offer_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EducativeOffer $educativeOffer, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(EducativeOfferType::class, $educativeOffer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                $extension = strtolower($imageFile->getClientOriginalExtension());

                if (!in_array($extension, $allowedExtensions)) {
                    $this->addFlash('error', 'Solo se permiten imágenes JPG, PNG o GIF');
                } else {
                    $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $extension = pathinfo($imageFile->getClientOriginalName(), PATHINFO_EXTENSION);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $extension;

                    $imageFile->move(
                        $this->getParameter('images_directory_educative_offer'),
                        $newFilename
                    );

                    $oldFilename = $educativeOffer->getImage();
                    if ($oldFilename) {
                        $oldImagePath = $this->getParameter('images_directory_educative_offer') . '/' . $oldFilename;
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }

                    $educativeOffer->setImage($newFilename);
                }
            }

            $entityManager->flush();

            return $this->redirect('/educative_offer', Response::HTTP_SEE_OTHER);
        }

        return $this->render('educative_offer/edit.html.twig', [
            'educative_offer' => $educativeOffer,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_educative_offer_delete', methods: ['POST'])]
    public function delete1(Request $request, EducativeOffer $educativeOffer, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$educativeOffer->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($educativeOffer);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_educative_offer_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_educative_offer_delete', methods: ['POST'])]
    public function delete(Request $request, EducativeOffer $educativeOffer, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $educativeOffer->getId(), $request->getPayload()->get('_token'))) {
            $imageFilename = $educativeOffer->getImage();

            if ($imageFilename) {
                $imagePath = $this->getParameter('images_directory_educative_offer') . '/' . $imageFilename;

                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            $entityManager->remove($educativeOffer);
            $entityManager->flush();
        }

        return $this->redirect('/educative_offer', Response::HTTP_SEE_OTHER);
    }

    #[Route('/educative_offer/{id}/delete-image', name: 'educative_offer_delete_image', methods: ['POST'])]
    public function deleteImage(Request $request, EducativeOffer $educativeOffer, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete_image' . $educativeOffer->getId(), $request->request->get('_token'))) {
            $filesystem = new Filesystem();

            $imagePath = $this->getParameter('images_directory_educative_offer') . '/' . $educativeOffer->getImage();

            if ($educativeOffer->getImage() && $filesystem->exists($imagePath)) {
                $filesystem->remove($imagePath);
            }

            $educativeOffer->setImage(null);
            $em->flush();

            $this->addFlash('success', 'Imagen borrada correctamente.');
        }

        return $this->redirectToRoute('app_educative_offer_edit', ['id' => $educativeOffer->getId()]);
    }
}
