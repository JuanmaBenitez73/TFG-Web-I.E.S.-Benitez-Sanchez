<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SchoolingInfoController extends AbstractController
{
    #[Route('/Información_Escolarizacion', name: 'app_schooling_info')]
    public function index(): Response
    {
        return $this->render('schooling_info/index.html.twig');
    }
}