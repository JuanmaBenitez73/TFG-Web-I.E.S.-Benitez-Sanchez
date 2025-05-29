<?php

namespace App\Controller;

use App\Entity\SchoolPlan;
use App\Form\SchoolPlanType;
use App\Repository\SchoolPlanRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Filesystem\Filesystem;

#[Route('/school_plan')]
class SchoolPlanController extends AbstractController
{
    #[Route('/', name: 'app_school_plan_index', methods: ['GET'])]
    public function index(SchoolPlanRepository $schoolPlanRepository): Response
    {
        return $this->render('school_plan/index.html.twig', [
            /* 'school_plans' => $schoolPlanRepository->findAll(), */ //Orden de creación
            'school_plans' => $schoolPlanRepository->findBy([], ['id' => 'DESC']),//Más reciente primero
        ]);
    }

    #[Route('/new', name: 'app_school_plan_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $school_plan = new SchoolPlan();

        $form = $this->createForm(SchoolPlanType::class, $school_plan);
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
                            $this->getParameter('images_directory_school_plan'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        $this->addFlash('error', 'No se pudo subir la imagen: ' . $e->getMessage());
                    }
                    $school_plan->setImage($newFilename);
                }
            }

            $entityManager->persist($school_plan);
            $entityManager->flush();

            return $this->redirect('/school_plan', Response::HTTP_SEE_OTHER);
        }

        return $this->render('school_plan/new.html.twig', [
            'school_plan' => $school_plan,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_school_plan_show', methods: ['GET'])]
    public function show(SchoolPlan $schoolPlan): Response
    {
        return $this->render('school_plan/show.html.twig', [
            'school_plan' => $schoolPlan,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_school_plan_edit', methods: ['GET', 'POST'])]
    public function edit1(Request $request, SchoolPlan $schoolPlan, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SchoolPlanType::class, $schoolPlan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_school_plan_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('school_plan/edit.html.twig', [
            'school_plan' => $schoolPlan,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_school_plan_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SchoolPlan $schoolPlan, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(SchoolPlanType::class, $schoolPlan);
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
                        $this->getParameter('images_directory_school_plan'),
                        $newFilename
                    );

                    $oldFilename = $schoolPlan->getImage();
                    if ($oldFilename) {
                        $oldImagePath = $this->getParameter('images_directory_school_plan') . '/' . $oldFilename;
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }

                    $schoolPlan->setImage($newFilename);
                }
            }

            $entityManager->flush();

            return $this->redirect('/school_plan', Response::HTTP_SEE_OTHER);
        }

        return $this->render('school_plan/edit.html.twig', [
            'school_plan' => $schoolPlan,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_school_plan_delete', methods: ['POST'])]
    public function delete(Request $request, SchoolPlan $schoolPlan, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $schoolPlan->getId(), $request->getPayload()->get('_token'))) {
            $imageFilename = $schoolPlan->getImage();

            if ($imageFilename) {
                $imagePath = $this->getParameter('images_directory_school_plan') . '/' . $imageFilename;

                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            $entityManager->remove($schoolPlan);
            $entityManager->flush();
        }

        return $this->redirect('/school_plan', Response::HTTP_SEE_OTHER);
    }

    #[Route('/school_plan/{id}/delete-image', name: 'school_plan_delete_image', methods: ['POST'])]
    public function deleteImage(Request $request, SchoolPlan $schoolPlan, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete_image' . $schoolPlan->getId(), $request->request->get('_token'))) {
            $filesystem = new Filesystem();

            $imagePath = $this->getParameter('images_directory_school_plan') . '/' . $schoolPlan->getImage();

            if ($schoolPlan->getImage() && $filesystem->exists($imagePath)) {
                $filesystem->remove($imagePath);
            }

            $schoolPlan->setImage(null);
            $em->flush();

            $this->addFlash('success', 'Imagen borrada correctamente.');
        }

        return $this->redirectToRoute('app_school_plan_edit', ['id' => $schoolPlan->getId()]);
    }
}
