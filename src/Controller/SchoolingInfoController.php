<?php

namespace App\Controller;

use App\Entity\SchoolingInfo;
use App\Form\SchoolingInfoType;
use App\Repository\SchoolingInfoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Filesystem\Filesystem;

#[Route('/schooling_info')]
class SchoolingInfoController extends AbstractController
{
    #[Route('/', name: 'app_schooling_info_index', methods: ['GET'])]
    public function index(SchoolingInfoRepository $schoolingInfoRepository): Response
    {
        return $this->render('schooling_info/index.html.twig', [
            /* 'schooling_infos' => $schoolingInfoRepository->findAll(), */ //Orden de creación
            'schooling_infos' => $schoolingInfoRepository->findBy([], ['id' => 'DESC']), //Más reciente primero
        ]);
    }

    #[Route('/new', name: 'app_schooling_info_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $schooling_info = new SchoolingInfo();

        $form = $this->createForm(SchoolingInfoType::class, $schooling_info);
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
                            $this->getParameter('images_directory_schooling_info'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        $this->addFlash('error', 'No se pudo subir la imagen: ' . $e->getMessage());
                    }
                    $schooling_info->setImage($newFilename);
                }
            }

            $entityManager->persist($schooling_info);
            $entityManager->flush();

            return $this->redirect('/schooling_info', Response::HTTP_SEE_OTHER);
        }

        return $this->render('schooling_info/new.html.twig', [
            'schooling_info' => $schooling_info,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_schooling_info_show', methods: ['GET'])]
    public function show(SchoolingInfo $schoolingInfo): Response
    {
        return $this->render('schooling_info/show.html.twig', [
            'schooling_info' => $schoolingInfo,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_schooling_info_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SchoolingInfo $schoolingInfo, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(SchoolingInfoType::class, $schoolingInfo);
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
                        $this->getParameter('images_directory_schooling_info'),
                        $newFilename
                    );

                    $oldFilename = $schoolingInfo->getImage();
                    if ($oldFilename) {
                        $oldImagePath = $this->getParameter('images_directory_schooling_info') . '/' . $oldFilename;
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }

                    $schoolingInfo->setImage($newFilename);
                }
            }

            $entityManager->flush();

            return $this->redirect('/schooling_info', Response::HTTP_SEE_OTHER);
        }

        return $this->render('schooling_info/edit.html.twig', [
            'schooling_info' => $schoolingInfo,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_schooling_info_delete', methods: ['POST'])]
    public function delete(Request $request, SchoolingInfo $schoolingInfo, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $schoolingInfo->getId(), $request->getPayload()->get('_token'))) {
            $imageFilename = $schoolingInfo->getImage();

            if ($imageFilename) {
                $imagePath = $this->getParameter('images_directory_schooling_info') . '/' . $imageFilename;

                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            $entityManager->remove($schoolingInfo);
            $entityManager->flush();
        }

        return $this->redirect('/schooling_info', Response::HTTP_SEE_OTHER);
    }

    #[Route('/schooling_info/{id}/delete-image', name: 'schooling_info_delete_image', methods: ['POST'])]
    public function deleteImage(Request $request, SchoolingInfo $schoolingInfo, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete_image' . $schoolingInfo->getId(), $request->request->get('_token'))) {
            $filesystem = new Filesystem();

            $imagePath = $this->getParameter('images_directory_schooling_info') . '/' . $schoolingInfo->getImage();

            if ($schoolingInfo->getImage() && $filesystem->exists($imagePath)) {
                $filesystem->remove($imagePath);
            }

            $schoolingInfo->setImage(null);
            $em->flush();

            $this->addFlash('success', 'Imagen borrada correctamente.');
        }

        return $this->redirectToRoute('app_schooling_info_edit', ['id' => $schoolingInfo->getId()]);
    }
}