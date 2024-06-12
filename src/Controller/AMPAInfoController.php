<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AMPAInfoController extends AbstractController
{
    #[Route('/Información_AMPA', name: 'app_info_AMPA')]
    public function index(): Response
    {
        return $this->render('info_ampa/index.html.twig');
    }
}