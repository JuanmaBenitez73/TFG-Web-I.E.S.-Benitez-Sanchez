<?php

namespace App\Controller;

use App\Entity\Affiliates;
use App\Entity\ContactInformation;
use App\Entity\Departments;
use App\Entity\Staff;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OrganizationChartController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    #[Route('/Organigrama', name: 'app_organization_chart')]
    public function index(): Response
    {
        $affiliates = $this->entityManager->getRepository(Affiliates::class)->findAll();
        $departments = $this->entityManager->getRepository(Departments::class)->findAll();
        $staff = $this->entityManager->getRepository(Staff::class)->findAll();
        $contactInformation = $this->entityManager->getRepository(ContactInformation::class)->findAll();
        return $this->render('organization_chart/index.html.twig', [
            'affiliates' => $affiliates,
            'departments' => $departments,
            'staff' => $staff,
            'contactInformation' => $contactInformation
        ]);
    }
}