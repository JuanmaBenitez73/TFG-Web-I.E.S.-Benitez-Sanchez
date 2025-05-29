<?php

namespace App\Controller;

use App\Entity\EducativePlanProgram;
use App\Form\EducativePlanProgramType;
use App\Repository\EducativePlanProgramRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Filesystem\Filesystem;

#[Route('/educative_plan_program')]
class EducativePlanProgramController extends AbstractController
{
    #[Route('/', name: 'app_educative_plan_program_index', methods: ['GET'])]
    public function index(EducativePlanProgramRepository $educativePlanProgramRepository): Response
    {
        return $this->render('educative_plan_program/index.html.twig', [
            /* 'educative_plan_programs' => $educativePlanProgramRepository->findAll(), */ //Orden de creación
            'educative_plan_programs' => $educativePlanProgramRepository->findBy([], ['id' => 'DESC']),//Más reciente primero
        ]);
    }

    #[Route('/new', name: 'app_educative_plan_program_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $educative_plan_program = new EducativePlanProgram();

        $form = $this->createForm(EducativePlanProgramType::class, $educative_plan_program);
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
                            $this->getParameter('images_directory_educative_plan_program'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        $this->addFlash('error', 'No se pudo subir la imagen: ' . $e->getMessage());
                    }
                    $educative_plan_program->setImage($newFilename);
                }
            }

            $entityManager->persist($educative_plan_program);
            $entityManager->flush();

            return $this->redirect('/educative_plan_program', Response::HTTP_SEE_OTHER);
        }

        return $this->render('educative_plan_program/new.html.twig', [
            'educative_plan_program' => $educative_plan_program,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_educative_plan_program_show', methods: ['GET'])]
    public function show(EducativePlanProgram $educativePlanProgram): Response
    {
        return $this->render('educative_plan_program/show.html.twig', [
            'educative_plan_program' => $educativePlanProgram,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_educative_plan_program_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EducativePlanProgram $educativePlanProgram, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(EducativePlanProgramType::class, $educativePlanProgram);
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
                        $this->getParameter('images_directory_educative_plan_program'),
                        $newFilename
                    );

                    $oldFilename = $educativePlanProgram->getImage();
                    if ($oldFilename) {
                        $oldImagePath = $this->getParameter('images_directory_educative_plan_program') . '/' . $oldFilename;
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }

                    $educativePlanProgram->setImage($newFilename);
                }
            }

            $entityManager->flush();

            return $this->redirect('/educative_plan_program', Response::HTTP_SEE_OTHER);
        }

        return $this->render('educative_plan_program/edit.html.twig', [
            'educative_plan_program' => $educativePlanProgram,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_educative_plan_program_delete', methods: ['POST'])]
    public function delete(Request $request, EducativePlanProgram $educativePlanProgram, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $educativePlanProgram->getId(), $request->getPayload()->get('_token'))) {
            $imageFilename = $educativePlanProgram->getImage();

            if ($imageFilename) {
                $imagePath = $this->getParameter('images_directory_educative_plan_program') . '/' . $imageFilename;

                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            $entityManager->remove($educativePlanProgram);
            $entityManager->flush();
        }

        return $this->redirect('/educative_plan_program', Response::HTTP_SEE_OTHER);
    }

    #[Route('/educative_plan_program/{id}/delete-image', name: 'educative_plan_program_delete_image', methods: ['POST'])]
    public function deleteImage(Request $request, EducativePlanProgram $educativePlanProgram, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete_image' . $educativePlanProgram->getId(), $request->request->get('_token'))) {
            $filesystem = new Filesystem();

            $imagePath = $this->getParameter('images_directory_educative_plan_program') . '/' . $educativePlanProgram->getImage();

            if ($educativePlanProgram->getImage() && $filesystem->exists($imagePath)) {
                $filesystem->remove($imagePath);
            }

            $educativePlanProgram->setImage(null);
            $em->flush();

            $this->addFlash('success', 'Imagen borrada correctamente.');
        }

        return $this->redirectToRoute('app_educative_plan_program_edit', ['id' => $educativePlanProgram->getId()]);
    }
}