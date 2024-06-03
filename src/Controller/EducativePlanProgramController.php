<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EducativePlanProgramController extends AbstractController
{
    #[Route('/Planes_y_programas_educativos', name: 'app_educative_plan_program')]
    public function index(): Response
    {
        return $this->render('educative_plan_program/index.html.twig');
    }
}