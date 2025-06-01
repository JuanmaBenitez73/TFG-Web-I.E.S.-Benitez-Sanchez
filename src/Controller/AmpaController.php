<?php

namespace App\Controller;

use App\Entity\Ampa;
use App\Form\AmpaType;
use App\Repository\AmpaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Filesystem\Filesystem;

#[Route('/ampa')]
class AmpaController extends AbstractController
{
    #[Route('/', name: 'app_ampa_index', methods: ['GET'])]
    public function index(AmpaRepository $ampaRepository): Response
    {
        return $this->render('ampa/index.html.twig', [
            /* 'ampas' => $ampaRepository->findAll(), */ //Orden de creación
            'ampas' => $ampaRepository->findBy([], ['id' => 'DESC']), //Más reciente primero
        ]);
    }

    #[Route('/new', name: 'app_ampa_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $ampa = new Ampa();

        $form = $this->createForm(AmpaType::class, $ampa);
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
                            $this->getParameter('images_directory_ampa'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        $this->addFlash('error', 'No se pudo subir la imagen: ' . $e->getMessage());
                    }
                    $ampa->setImage($newFilename);
                }
            }

            $entityManager->persist($ampa);
            $entityManager->flush();

            return $this->redirect('/ampa', Response::HTTP_SEE_OTHER);
        }

        return $this->render('ampa/new.html.twig', [
            'ampa' => $ampa,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ampa_show', methods: ['GET'])]
    public function show(Ampa $ampa): Response
    {
        return $this->render('ampa/show.html.twig', [
            'ampa' => $ampa,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_ampa_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Ampa $ampa, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(AmpaType::class, $ampa);
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
                        $this->getParameter('images_directory_ampa'),
                        $newFilename
                    );

                    $oldFilename = $ampa->getImage();
                    if ($oldFilename) {
                        $oldImagePath = $this->getParameter('images_directory_ampa') . '/' . $oldFilename;
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }

                    $ampa->setImage($newFilename);
                }
            }

            $entityManager->flush();

            return $this->redirect('/ampa', Response::HTTP_SEE_OTHER);
        }

        return $this->render('ampa/edit.html.twig', [
            'ampa' => $ampa,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ampa_delete', methods: ['POST'])]
    public function delete(Request $request, Ampa $ampa, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $ampa->getId(), $request->getPayload()->get('_token'))) {
            $imageFilename = $ampa->getImage();

            if ($imageFilename) {
                $imagePath = $this->getParameter('images_directory_ampa') . '/' . $imageFilename;

                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            $entityManager->remove($ampa);
            $entityManager->flush();
        }

        return $this->redirect('/ampa', Response::HTTP_SEE_OTHER);
    }

    #[Route('/ampa/{id}/delete-image', name: 'ampa_delete_image', methods: ['POST'])]
    public function deleteImage(Request $request, Ampa $ampa, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete_image' . $ampa->getId(), $request->request->get('_token'))) {
            $filesystem = new Filesystem();

            $imagePath = $this->getParameter('images_directory_ampa') . '/' . $ampa->getImage();

            if ($ampa->getImage() && $filesystem->exists($imagePath)) {
                $filesystem->remove($imagePath);
            }

            $ampa->setImage(null);
            $em->flush();

            $this->addFlash('success', 'Imagen borrada correctamente.');
        }

        return $this->redirectToRoute('app_ampa_edit', ['id' => $ampa->getId()]);
    }
}
