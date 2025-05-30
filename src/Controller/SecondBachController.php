<?php

namespace App\Controller;

use App\Entity\SecondBach;
use App\Form\SecondBachType;
use App\Repository\SecondBachRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Filesystem\Filesystem;

#[Route('/second_bach')]
class SecondBachController extends AbstractController
{
    #[Route('/', name: 'app_second_bach_index', methods: ['GET'])]
    public function index(SecondBachRepository $secondBachRepository): Response
    {
        return $this->render('second_bach/index.html.twig', [
            /* 'second_bachs' => $secondBachRepository->findAll(), */ //Orden de creación
            'second_bachs' => $secondBachRepository->findBy([], ['id' => 'DESC']), //Más reciente primero
        ]);
    }

    #[Route('/new', name: 'app_second_bach_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $second_bach = new SecondBach();

        $form = $this->createForm(SecondBachType::class, $second_bach);
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
                            $this->getParameter('images_directory_second_bach'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        $this->addFlash('error', 'No se pudo subir la imagen: ' . $e->getMessage());
                    }
                    $second_bach->setImage($newFilename);
                }
            }

            $entityManager->persist($second_bach);
            $entityManager->flush();

            return $this->redirect('/second_bach', Response::HTTP_SEE_OTHER);
        }

        return $this->render('second_bach/new.html.twig', [
            'second_bach' => $second_bach,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_second_bach_show', methods: ['GET'])]
    public function show(SecondBach $secondBach): Response
    {
        return $this->render('second_bach/show.html.twig', [
            'second_bach' => $secondBach,
        ]);
    }

     #[Route('/{id}/edit', name: 'app_second_bach_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SecondBach $secondBach, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(SecondBachType::class, $secondBach);
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
                        $this->getParameter('images_directory_second_bach'),
                        $newFilename
                    );

                    $oldFilename = $secondBach->getImage();
                    if ($oldFilename) {
                        $oldImagePath = $this->getParameter('images_directory_second_bach') . '/' . $oldFilename;
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }

                    $secondBach->setImage($newFilename);
                }
            }

            $entityManager->flush();

            return $this->redirect('/second_bach', Response::HTTP_SEE_OTHER);
        }

        return $this->render('second_bach/edit.html.twig', [
            'second_bach' => $secondBach,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_second_bach_delete', methods: ['POST'])]
    public function delete(Request $request, SecondBach $secondBach, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $secondBach->getId(), $request->getPayload()->get('_token'))) {
            $imageFilename = $secondBach->getImage();

            if ($imageFilename) {
                $imagePath = $this->getParameter('images_directory_second_bach') . '/' . $imageFilename;

                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            $entityManager->remove($secondBach);
            $entityManager->flush();
        }

        return $this->redirect('/second_bach', Response::HTTP_SEE_OTHER);
    }

    #[Route('/second_bach/{id}/delete-image', name: 'second_bach_delete_image', methods: ['POST'])]
    public function deleteImage(Request $request, SecondBach $secondBach, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete_image' . $secondBach->getId(), $request->request->get('_token'))) {
            $filesystem = new Filesystem();

            $imagePath = $this->getParameter('images_directory_second_bach') . '/' . $secondBach->getImage();

            if ($secondBach->getImage() && $filesystem->exists($imagePath)) {
                $filesystem->remove($imagePath);
            }

            $secondBach->setImage(null);
            $em->flush();

            $this->addFlash('success', 'Imagen borrada correctamente.');
        }

        return $this->redirectToRoute('app_second_bach_edit', ['id' => $secondBach->getId()]);
    }
}
