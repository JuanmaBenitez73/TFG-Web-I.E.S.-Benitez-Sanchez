<?php

namespace App\Controller;

use App\Entity\StudentsPendingSubjects;
use App\Form\StudentsPendingSubjectsType;
use App\Repository\StudentsPendingSubjectsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Filesystem\Filesystem;

#[Route('/students_pending_subjects')]
class StudentsPendingSubjectsController extends AbstractController
{
    #[Route('/', name: 'app_students_pending_subjects_index', methods: ['GET'])]
    public function index(StudentsPendingSubjectsRepository $studentsPendingSubjectsRepository): Response
    {
        return $this->render('students_pending_subjects/index.html.twig', [
            /* 'students_pending_subjectss' => $studentsPendingSubjectsRepository->findAll(), */ //Orden de creación
            'students_pending_subjectss' => $studentsPendingSubjectsRepository->findBy([], ['id' => 'DESC']), //Más reciente primero
        ]);
    }

    #[Route('/new', name: 'app_students_pending_subjects_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $students_pending_subjects = new StudentsPendingSubjects();

        $form = $this->createForm(StudentsPendingSubjectsType::class, $students_pending_subjects);
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
                            $this->getParameter('images_directory_students_pending_subjects'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        $this->addFlash('error', 'No se pudo subir la imagen: ' . $e->getMessage());
                    }
                    $students_pending_subjects->setImage($newFilename);
                }
            }

            $entityManager->persist($students_pending_subjects);
            $entityManager->flush();

            return $this->redirect('/students_pending_subjects', Response::HTTP_SEE_OTHER);
        }

        return $this->render('students_pending_subjects/new.html.twig', [
            'students_pending_subjects' => $students_pending_subjects,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_students_pending_subjects_show', methods: ['GET'])]
    public function show(StudentsPendingSubjects $studentsPendingSubject): Response
    {
        return $this->render('students_pending_subjects/show.html.twig', [
            'students_pending_subject' => $studentsPendingSubject,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_students_pending_subjects_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, StudentsPendingSubjects $studentsPendingSubjects, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(StudentsPendingSubjectsType::class, $studentsPendingSubjects);
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
                        $this->getParameter('images_directory_students_pending_subjects'),
                        $newFilename
                    );

                    $oldFilename = $studentsPendingSubjects->getImage();
                    if ($oldFilename) {
                        $oldImagePath = $this->getParameter('images_directory_students_pending_subjects') . '/' . $oldFilename;
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }

                    $studentsPendingSubjects->setImage($newFilename);
                }
            }

            $entityManager->flush();

            return $this->redirect('/students_pending_subjects', Response::HTTP_SEE_OTHER);
        }

        return $this->render('students_pending_subjects/edit.html.twig', [
            'students_pending_subjects' => $studentsPendingSubjects,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_students_pending_subjects_delete', methods: ['POST'])]
    public function delete(Request $request, StudentsPendingSubjects $studentsPendingSubjects, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $studentsPendingSubjects->getId(), $request->getPayload()->get('_token'))) {
            $imageFilename = $studentsPendingSubjects->getImage();

            if ($imageFilename) {
                $imagePath = $this->getParameter('images_directory_students_pending_subjects') . '/' . $imageFilename;

                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            $entityManager->remove($studentsPendingSubjects);
            $entityManager->flush();
        }

        return $this->redirect('/students_pending_subjects', Response::HTTP_SEE_OTHER);
    }

    #[Route('/students_pending_subjects/{id}/delete-image', name: 'students_pending_subjects_delete_image', methods: ['POST'])]
    public function deleteImage(Request $request, StudentsPendingSubjects $studentsPendingSubjects, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete_image' . $studentsPendingSubjects->getId(), $request->request->get('_token'))) {
            $filesystem = new Filesystem();

            $imagePath = $this->getParameter('images_directory_students_pending_subjects') . '/' . $studentsPendingSubjects->getImage();

            if ($studentsPendingSubjects->getImage() && $filesystem->exists($imagePath)) {
                $filesystem->remove($imagePath);
            }

            $studentsPendingSubjects->setImage(null);
            $em->flush();

            $this->addFlash('success', 'Imagen borrada correctamente.');
        }

        return $this->redirectToRoute('app_students_pending_subjects_edit', ['id' => $studentsPendingSubjects->getId()]);
    }
}
