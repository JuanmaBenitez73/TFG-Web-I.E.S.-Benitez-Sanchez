<?php

namespace App\Controller;

use App\Entity\Dace;
use App\Form\DaceType;
use App\Repository\DaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Filesystem\Filesystem;

#[Route('/dace')]
class DaceController extends AbstractController
{
    #[Route('/', name: 'app_dace_index', methods: ['GET'])]
    public function index(DaceRepository $daceRepository): Response
    {
        return $this->render('dace/index.html.twig', [
            /* 'daces' => $daceRepository->findAll(), */ //Orden de creación
            'daces' => $daceRepository->findBy([], ['id' => 'DESC']), //Más reciente primero
        ]);
    }

    #[Route('/new', name: 'app_dace_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $dace = new Dace();

        $form = $this->createForm(DaceType::class, $dace);
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
                            $this->getParameter('images_directory_dace'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        $this->addFlash('error', 'No se pudo subir la imagen: ' . $e->getMessage());
                    }
                    $dace->setImage($newFilename);
                }
            }

            $entityManager->persist($dace);
            $entityManager->flush();

            return $this->redirect('/dace', Response::HTTP_SEE_OTHER);
        }

        return $this->render('dace/new.html.twig', [
            'dace' => $dace,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_dace_show', methods: ['GET'])]
    public function show(Dace $dace): Response
    {
        return $this->render('dace/show.html.twig', [
            'dace' => $dace,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_dace_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Dace $dace, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(DaceType::class, $dace);
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
                        $this->getParameter('images_directory_dace'),
                        $newFilename
                    );

                    $oldFilename = $dace->getImage();
                    if ($oldFilename) {
                        $oldImagePath = $this->getParameter('images_directory_dace') . '/' . $oldFilename;
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }

                    $dace->setImage($newFilename);
                }
            }

            $entityManager->flush();

            return $this->redirect('/dace', Response::HTTP_SEE_OTHER);
        }

        return $this->render('dace/edit.html.twig', [
            'dace' => $dace,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_dace_delete', methods: ['POST'])]
    public function delete(Request $request, Dace $dace, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $dace->getId(), $request->getPayload()->get('_token'))) {
            $imageFilename = $dace->getImage();

            if ($imageFilename) {
                $imagePath = $this->getParameter('images_directory_dace') . '/' . $imageFilename;

                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            $entityManager->remove($dace);
            $entityManager->flush();
        }

        return $this->redirect('/dace', Response::HTTP_SEE_OTHER);
    }

    #[Route('/dace/{id}/delete-image', name: 'dace_delete_image', methods: ['POST'])]
    public function deleteImage(Request $request, Dace $dace, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete_image' . $dace->getId(), $request->request->get('_token'))) {
            $filesystem = new Filesystem();

            $imagePath = $this->getParameter('images_directory_dace') . '/' . $dace->getImage();

            if ($dace->getImage() && $filesystem->exists($imagePath)) {
                $filesystem->remove($imagePath);
            }

            $dace->setImage(null);
            $em->flush();

            $this->addFlash('success', 'Imagen borrada correctamente.');
        }

        return $this->redirectToRoute('app_dace_edit', ['id' => $dace->getId()]);
    }
}
