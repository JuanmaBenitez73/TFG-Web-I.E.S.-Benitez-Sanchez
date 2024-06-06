<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProjectManagementController extends AbstractController
{
    #[Route('/Plan_centro/Proyecto_Gestión', name: 'app_school_plan_project_management')]
    public function index(): Response
    {
        return $this->render('school_plan/project_management/index.html.twig');
    }
}