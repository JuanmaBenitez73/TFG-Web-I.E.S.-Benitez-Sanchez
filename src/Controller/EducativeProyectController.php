<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EducativeProyectController extends AbstractController
{
    #[Route('/Plan_centro/Proyecto_educativo', name: 'app_school_plan_educative_proyect')]
    public function index(): Response
    {
        return $this->render('school_plan/educative_proyect/index.html.twig');
    }
}