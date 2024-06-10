<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DACEInfoController extends AbstractController
{
    #[Route('/Información_DACE', name: 'app_DACE_info')]
    public function index(): Response
    {
        return $this->render('dace_info/index.html.twig');
    }
}