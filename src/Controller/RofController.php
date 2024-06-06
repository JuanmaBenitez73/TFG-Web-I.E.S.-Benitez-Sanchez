<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RofController extends AbstractController
{
    #[Route('/Plan_centro/ROF', name: 'app_school_plan_rof')]
    public function index(): Response
    {
        return $this->render('school_plan/rof/index.html.twig');
    }
}