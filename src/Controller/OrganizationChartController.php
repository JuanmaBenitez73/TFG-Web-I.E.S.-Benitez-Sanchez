<?php

namespace App\Controller;

use App\Entity\OrganizationChart;
use App\Form\OrganizationChartType;
use App\Repository\OrganizationChartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Filesystem\Filesystem;

#[Route('/organization_chart')]
class OrganizationChartController extends AbstractController
{
    #[Route('/', name: 'app_organization_chart_index', methods: ['GET'])]
    public function index(OrganizationChartRepository $organizationChartRepository): Response
    {
        return $this->render('organization_chart/index.html.twig', [
            'organization_charts' => $organizationChartRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_organization_chart_new', methods: ['GET', 'POST'])]
    public function new1(Request $request, EntityManagerInterface $entityManager): Response
    {
        $organizationChart = new OrganizationChart();
        $form = $this->createForm(OrganizationChartType::class, $organizationChart);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($organizationChart);
            $entityManager->flush();

            return $this->redirectToRoute('app_organization_chart_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('organization_chart/new.html.twig', [
            'organization_chart' => $organizationChart,
            'form' => $form,
        ]);
    }

    #[Route('/new', name: 'app_organization_chart_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $organization_chart = new OrganizationChart();

        $form = $this->createForm(OrganizationChartType::class, $organization_chart);
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
                    $organization_chart->setImage($newFilename);
                }
            }

            $entityManager->persist($organization_chart);
            $entityManager->flush();

            return $this->redirect('/organization_chart', Response::HTTP_SEE_OTHER);
        }

        return $this->render('organization_chart/new.html.twig', [
            'organization_chart' => $organization_chart,
            'form' => $form,
        ]);
    }



    #[Route('/{id}', name: 'app_organization_chart_show', methods: ['GET'])]
    public function show(OrganizationChart $organizationChart): Response
    {
        return $this->render('organization_chart/show.html.twig', [
            'organization_chart' => $organizationChart,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_organization_chart_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, OrganizationChart $organizationChart, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OrganizationChartType::class, $organizationChart);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_organization_chart_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('organization_chart/edit.html.twig', [
            'organization_chart' => $organizationChart,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_organization_chart_delete', methods: ['POST'])]
    public function delete(Request $request, OrganizationChart $organizationChart, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$organizationChart->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($organizationChart);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_organization_chart_index', [], Response::HTTP_SEE_OTHER);
    }
}
