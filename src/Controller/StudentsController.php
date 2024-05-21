<?php

namespace App\Controller;

use App\Entity\Students;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class StudentsController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    #[Route('/students', name: 'app_students', methods: ['GET', 'HEAD'])]
    public function index(): Response
    {
        $students = $this->entityManager->getRepository(Students::class)->findAll();

        return $this->render('students/index.html.twig', [
            'students' => $students,
        ]);
    }
}
