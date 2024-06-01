<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SchoolPlanController extends AbstractController
{
    #[Route('/Plan_centro', name: 'app_school_plan')]
    public function index(): Response
    {
        return $this->render('school_plan/index.html.twig');
    }
}