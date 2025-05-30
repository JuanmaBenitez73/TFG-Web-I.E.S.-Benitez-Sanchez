<?php

namespace App\Controller;

use App\Entity\FP;
use App\Form\FPType;
use App\Repository\FPRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Filesystem\Filesystem;

#[Route('/fp')]
class FPController extends AbstractController
{
    #[Route('/', name: 'app_fp_index', methods: ['GET'])]
    public function index(FPRepository $fPRepository): Response
    {
        return $this->render('fp/index.html.twig', [
            /* 'fps' => $fPRepository->findAll(), */ //Orden de creación
            'fps' => $fPRepository->findBy([], ['id' => 'DESC']), //Más reciente primero
        ]);
    }

    #[Route('/new', name: 'app_fp_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $fp = new FP();

        $form = $this->createForm(FPType::class, $fp);
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
                            $this->getParameter('images_directory_fp'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        $this->addFlash('error', 'No se pudo subir la imagen: ' . $e->getMessage());
                    }
                    $fp->setImage($newFilename);
                }
            }

            $entityManager->persist($fp);
            $entityManager->flush();

            return $this->redirect('/fp', Response::HTTP_SEE_OTHER);
        }

        return $this->render('fp/new.html.twig', [
            'fp' => $fp,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_fp_show', methods: ['GET'])]
    public function show(FP $fP): Response
    {
        return $this->render('fp/show.html.twig', [
            'fp' => $fP,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_fp_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, FP $organizationChart, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(FPType::class, $organizationChart);
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
                        $this->getParameter('images_directory_fp'),
                        $newFilename
                    );

                    $oldFilename = $organizationChart->getImage();
                    if ($oldFilename) {
                        $oldImagePath = $this->getParameter('images_directory_fp') . '/' . $oldFilename;
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }

                    $organizationChart->setImage($newFilename);
                }
            }

            $entityManager->flush();

            return $this->redirect('/fp', Response::HTTP_SEE_OTHER);
        }

        return $this->render('fp/edit.html.twig', [
            'fp' => $organizationChart,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_fp_delete', methods: ['POST'])]
    public function delete(Request $request, FP $fp, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $fp->getId(), $request->getPayload()->get('_token'))) {
            $imageFilename = $fp->getImage();

            if ($imageFilename) {
                $imagePath = $this->getParameter('images_directory_fp') . '/' . $imageFilename;

                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            $entityManager->remove($fp);
            $entityManager->flush();
        }

        return $this->redirect('/fp', Response::HTTP_SEE_OTHER);
    }

    #[Route('/fp/{id}/delete-image', name: 'fp_delete_image', methods: ['POST'])]
    public function deleteImage(Request $request, FP $fp, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete_image' . $fp->getId(), $request->request->get('_token'))) {
            $filesystem = new Filesystem();

            $imagePath = $this->getParameter('images_directory_fp') . '/' . $fp->getImage();

            if ($fp->getImage() && $filesystem->exists($imagePath)) {
                $filesystem->remove($imagePath);
            }

            $fp->setImage(null);
            $em->flush();

            $this->addFlash('success', 'Imagen borrada correctamente.');
        }

        return $this->redirectToRoute('app_fp_edit', ['id' => $fp->getId()]);
    }
}
