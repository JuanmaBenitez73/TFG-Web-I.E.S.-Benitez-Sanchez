<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class InfoStudentsController extends AbstractController
{
    #[Route('/Información_Estudiantes_Recuperación', name: 'app_info_students')]
    public function index(): Response
    {
        return $this->render('info_students/index.html.twig');
    }
}